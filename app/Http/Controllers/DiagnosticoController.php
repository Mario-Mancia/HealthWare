<?php

namespace App\Http\Controllers;

use App\Models\Diagnostico;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DiagnosticoController extends Controller
{
    public function index(): View
    {
        $datos = [
            'diagnosticos' => Diagnostico::mostrarTodos(),
        ];

        return view('diagnostico.diagnosticos', $datos);
    }

    public function insertarDiagnostico()
    {
        // La validación mediante FormRequest se agregará posteriormente.
    }

    public function buscarDiagnosticoXId(int $id): JsonResponse
    {
        return response()->json(
            Diagnostico::buscarDiagnosticoXId($id)
        );
    }

    public function actualizarDiagnostico()
    {
        // La validación mediante FormRequest se agregará posteriormente.
    }

    public function eliminarDiagnostico(
        Diagnostico $diagnostico
    ): RedirectResponse {
        if (Diagnostico::eliminar($diagnostico->getKey())) {
            return redirect()
                ->route('diagnosticos')
                ->with(
                    'success',
                    'Diagnóstico eliminado correctamente'
                );
        }

        return redirect()
            ->route('diagnosticos')
            ->with(
                'error',
                'Diagnóstico no eliminado'
            );
    }
}
