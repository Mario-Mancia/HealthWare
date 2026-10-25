<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsertarDetalleFacturaRequest;
use App\Models\Bitacora;
use App\Models\DetalleFactura;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DetalleFacturaController extends Controller
{
    public function index(): View
    {
        return view('detalle_facturas.detalle_facturas', [
            'detallesFacturas' => DetalleFactura::mostrarTodos(),
        ]);
    }

    public function mostrarTodos(): JsonResponse
    {
        return response()->json(
            DetalleFactura::mostrarTodos()
        );
    }

    public function insertarDetalleFactura(
        InsertarDetalleFacturaRequest $request
    ): RedirectResponse {
        $detalleFactura = DetalleFactura::insertar(
            $request->validated()
        );

        Bitacora::registrar(
            Bitacora::CREADO,
            'DETALLE_FACTURAS',
            $detalleFactura->getKey(),
            'Se agregó un servicio a una factura.'
        );

        return redirect()
            ->route('detalle_facturas')
            ->with(
                'success',
                'El servicio se agregó a la factura correctamente.'
            );
    }

    public function buscarDetalleFactura(int $id): JsonResponse
    {
        $detalleFactura = DetalleFactura::buscarDetalleFacturaXId($id);

        if (!$detalleFactura) {
            return response()->json([
                'mensaje' => 'El detalle de la factura no existe.',
            ], 404);
        }

        return response()->json($detalleFactura);
    }
}
