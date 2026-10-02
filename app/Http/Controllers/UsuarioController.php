<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActualizarUsuarioRequest;
use App\Http\Requests\InsertarUsuarioRequest;
use App\Models\Estado;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    /**
     * Muestra todos los usuarios registrados.
     */
    public function index(): View
    {
        $datos = [
            'usuarios' => Usuario::mostrarTodos(),
            'roles' => Rol::mostrarTodos(),
            'estados' => Estado::mostrarTodos(),
        ];

        return view('usuario.usuarios', $datos);
    }

    /**
     * Inserta un nuevo usuario.
     */
    public function insertarUsuario(
        InsertarUsuarioRequest $request
    ): RedirectResponse {
        Usuario::insertar($request->validated());

        return redirect()
            ->route('usuarios')
            ->with(
                'success',
                'Usuario insertado correctamente'
            );
    }

    /**
     * Busca un usuario por su ID.
     */
    public function buscarUsuarioXId(int $id): JsonResponse
    {
        $usuario = Usuario::buscarUsuarioXId($id);

        if (!$usuario) {
            return response()->json(
                [
                    'message' => 'Usuario no encontrado.'
                ],
                404
            );
        }

        return response()->json($usuario);
    }

    /**
     * Actualiza un usuario existente.
     */
    public function actualizarUsuario(
        ActualizarUsuarioRequest $request,
        Usuario $usuario
    ): RedirectResponse {
        if (
            Usuario::actualizar(
                $request->validated(),
                $usuario->getKey()
            )
        ) {
            return redirect()
                ->route('usuarios')
                ->with(
                    'success',
                    'Usuario actualizado correctamente'
                );
        }

        return redirect()
            ->route('usuarios')
            ->with(
                'error',
                'Usuario no actualizado'
            );
    }

    /**
     * Elimina un usuario existente.
     */
    public function eliminarUsuario(
        Usuario $usuario
    ): RedirectResponse {
        if (Usuario::eliminar($usuario->getKey())) {
            return redirect()
                ->route('usuarios')
                ->with(
                    'success',
                    'Usuario eliminado correctamente'
                );
        }

        return redirect()
            ->route('usuarios')
            ->with(
                'error',
                'Usuario no eliminado'
            );
    }
}

