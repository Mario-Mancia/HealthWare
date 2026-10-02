<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EspecialidadController extends Controller
{
    public function index(): View
    {
        $datos = [
            'especialidades' => Especialidad::mostrarTodos(),
        ];

        return view('especialidad.especialidades', $datos);
    }

    public function insertarEspecialidad()
    {
        // La validación mediante FormRequest se agregará posteriormente.
    }

    public function buscarEspecialidadXId(int $id): JsonResponse
    {
        return response()->json(
            Especialidad::buscarEspecialidadXId($id)
        );
    }

    public function actualizarEspecialidad()
    {
        // La validación mediante FormRequest se agregará posteriormente.
    }

    public function eliminarEspecialidad(
        Especialidad $especialidad
    ): RedirectResponse {
        if (Especialidad::eliminar($especialidad->getKey())) {
            return redirect()
                ->route('especialidades')
                ->with(
                    'success',
                    'Especialidad eliminada correctamente'
                );
        }

        return redirect()
            ->route('especialidades')
            ->with(
                'error',
                'Especialidad no eliminada'
            );
    }
}
