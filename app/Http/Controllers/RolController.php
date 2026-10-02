<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RolController extends Controller
{
    /**
     * Muestra todos los roles registrados.
     */
    public function index(): View
    {
        $datos = [
            'roles' => Rol::mostrarTodos(),
        ];

        return view('rol.roles', $datos);
    }

    /**
     * Inserta un nuevo rol.
     */
    public function insertarRol()
    {
        // La validación mediante FormRequest se agregará
        // posteriormente cuando configuremos los formularios.
    }

    /**
     * Busca un rol por su identificador.
     */
    public function buscarRolXId(int $id): JsonResponse
    {
        return response()->json(
            Rol::buscarRolXId($id)
        );
    }

    /**
     * Actualiza un rol existente.
     */
    public function actualizarRol()
    {
        // La validación mediante FormRequest se agregará
        // posteriormente cuando configuremos los formularios.
    }

    /**
     * Elimina un rol existente.
     */
    public function eliminarRol(Rol $rol): RedirectResponse
    {
        if (Rol::eliminar($rol->getKey())) {
            return redirect()
                ->route('roles')
                ->with('success', 'Rol eliminado correctamente');
        }

        return redirect()
            ->route('roles')
            ->with('error', 'No se pudo eliminar el rol.');
    }
}
