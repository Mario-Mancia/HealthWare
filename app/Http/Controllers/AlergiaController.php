<?php

namespace App\Http\Controllers;

use App\Models\Alergia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AlergiaController extends Controller
{
    public function index(): View
    {
        $datos = [
            'alergias' => Alergia::mostrarTodos(),
        ];

        return view('alergia.alergias', $datos);
    }

    public function insertarAlergia()
    {
        // La validación mediante FormRequest se agregará posteriormente.
    }

    public function buscarAlergiaXId(int $id): JsonResponse
    {
        return response()->json(
            Alergia::buscarAlergiaXId($id)
        );
    }

    public function actualizarAlergia()
    {
        // La validación mediante FormRequest se agregará posteriormente.
    }

    public function eliminarAlergia(
        Alergia $alergia
    ): RedirectResponse {
        if (Alergia::eliminar($alergia->getKey())) {
            return redirect()
                ->route('alergias')
                ->with(
                    'success',
                    'Alergia eliminada correctamente'
                );
        }

        return redirect()
            ->route('alergias')
            ->with(
                'error',
                'Alergia no eliminada'
            );
    }
}
