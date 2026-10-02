<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EstadoController extends Controller
{
    /**
     * Muestra todos los estados registrados.
     */
    public function index(): View
    {
        $datos = [
            'estados' => Estado::mostrarTodos(),
        ];

        return view('estado.estados', $datos);
    }

    /**
     * Inserta un nuevo estado.
     */
    public function insertarEstado()
    {
        // La validación mediante FormRequest se agregará posteriormente.
    }

    /**
     * Busca un estado por su identificador.
     */
    public function buscarEstadoXId(int $id): JsonResponse
    {
        return response()->json(
            Estado::buscarEstadoXId($id)
        );
    }

    /**
     * Actualiza un estado existente.
     */
    public function actualizarEstado()
    {
        // La validación mediante FormRequest se agregará posteriormente.
    }

    /**
     * Elimina un estado existente.
     */
    public function eliminarEstado(Estado $estado): RedirectResponse
    {
        if (Estado::eliminar($estado->getKey())) {
            return redirect()
                ->route('estados')
                ->with('success', 'Estado eliminado correctamente');
        }

        return redirect()
            ->route('estados')
            ->with('error', 'Estado no eliminado');
    }
}
