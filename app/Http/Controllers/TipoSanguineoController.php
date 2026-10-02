<?php

namespace App\Http\Controllers;

use App\Models\TipoSanguineo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TipoSanguineoController extends Controller
{
    /**
     * Muestra todos los tipos sanguíneos registrados.
     */
    public function index(): View
    {
        $datos = [
            'tiposSanguineos' => TipoSanguineo::mostrarTodos(),
        ];

        return view('tipo_sanguineo.tipos_sanguineos', $datos);
    }

    /**
     * Inserta un nuevo tipo sanguíneo.
     */
    public function insertarTipoSanguineo()
    {
        // La validación mediante FormRequest se agregará posteriormente.
    }

    /**
     * Busca un tipo sanguíneo por su identificador.
     */
    public function buscarTipoSanguineoXId(int $id): JsonResponse
    {
        return response()->json(
            TipoSanguineo::buscarTipoSanguineoXId($id)
        );
    }

    /**
     * Actualiza un tipo sanguíneo existente.
     */
    public function actualizarTipoSanguineo()
    {
        // La validación mediante FormRequest se agregará posteriormente.
    }

    /**
     * Elimina un tipo sanguíneo existente.
     */
    public function eliminarTipoSanguineo(
        TipoSanguineo $tipoSanguineo
    ): RedirectResponse {
        if (TipoSanguineo::eliminar($tipoSanguineo->getKey())) {
            return redirect()
                ->route('tipos-sanguineos')
                ->with(
                    'success',
                    'Tipo sanguíneo eliminado correctamente'
                );
        }

        return redirect()
            ->route('tipos-sanguineos')
            ->with(
                'error',
                'Tipo sanguíneo no eliminado'
            );
    }
}
