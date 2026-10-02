<?php

namespace App\Http\Controllers;


use App\Models\CondicionMedica;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CondicionMedicaController extends Controller
{
    public function index(): View
    {
        $datos = [
            'condicionesMedicas' => CondicionMedica::mostrarTodos(),
        ];

        return view('condicion_medica.condiciones_medicas', $datos);
    }

    public function insertarCondicionMedica()
    {
        // La validación mediante FormRequest se agregará posteriormente.
    }

    public function buscarCondicionMedicaXId(int $id): JsonResponse
    {
        return response()->json(
            CondicionMedica::buscarCondicionMedicaXId($id)
        );
    }

    public function actualizarCondicionMedica()
    {
        // La validación mediante FormRequest se agregará posteriormente.
    }

    public function eliminarCondicionMedica(
        CondicionMedica $condicionMedica
    ): RedirectResponse {
        if (CondicionMedica::eliminar($condicionMedica->getKey())) {
            return redirect()
                ->route('condiciones-medicas')
                ->with(
                    'success',
                    'Condición médica eliminada correctamente'
                );
        }

        return redirect()
            ->route('condiciones-medicas')
            ->with(
                'error',
                'Condición médica no eliminada'
            );
    }
}
