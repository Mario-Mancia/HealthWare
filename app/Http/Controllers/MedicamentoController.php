<?php

namespace App\Http\Controllers;

use App\Models\Medicamento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;


class MedicamentoController extends Controller
{
    public function index(): View
    {
        $datos = [
            'medicamentos.css' => Medicamento::mostrarTodos(),
        ];

        return view('medicamentos.index', $datos);
    }

    public function insertarMedicamento()
    {
        // La validación mediante FormRequest se agregará posteriormente.
    }

    public function buscarMedicamentoXId(int $id): JsonResponse
    {
        return response()->json(
            Medicamento::buscarMedicamentoXId($id)
        );
    }

    public function actualizarMedicamento()
    {
        // La validación mediante FormRequest se agregará posteriormente.
    }

    public function eliminarMedicamento(
        Medicamento $medicamento
    ): RedirectResponse {
        if (Medicamento::eliminar($medicamento->getKey())) {
            return redirect()
                ->route('medicamentos.css')
                ->with(
                    'success',
                    'Medicamento eliminado correctamente'
                );
        }

        return redirect()
            ->route('medicamentos.css')
            ->with(
                'error',
                'Medicamento no eliminado'
            );
    }
}
