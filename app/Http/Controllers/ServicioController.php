<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ServicioController extends Controller
{
    public function index(): View
    {
        $datos = [
            'servicios' => Servicio::mostrarTodos(),
        ];

        return view('servicio.servicios', $datos);
    }

    public function insertarServicio()
    {
        // La validación mediante FormRequest se agregará posteriormente.
    }

    public function buscarServicioXId(int $id): JsonResponse
    {
        return response()->json(
            Servicio::buscarServicioXId($id)
        );
    }

    public function actualizarServicio()
    {
        // La validación mediante FormRequest se agregará posteriormente.
    }

    public function eliminarServicio(
        Servicio $servicio
    ): RedirectResponse {
        if (Servicio::eliminar($servicio->getKey())) {
            return redirect()
                ->route('servicios')
                ->with(
                    'success',
                    'Servicio eliminado correctamente'
                );
        }

        return redirect()
            ->route('servicios')
            ->with(
                'error',
                'Servicio no eliminado'
            );
    }
}
