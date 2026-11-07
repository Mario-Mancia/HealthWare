<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsertarFacturaRequest;
use App\Models\Bitacora;
use App\Models\Factura;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FacturaController extends Controller
{
    public function index(): View
    {
        return view('facturacion.index', [
            'facturas' => Factura::mostrarTodos(),
        ]);
    }

    public function mostrarTodos(): JsonResponse
    {
        return response()->json(
            Factura::mostrarTodos()
        );
    }

    public function insertarFactura(
        InsertarFacturaRequest $request
    ): RedirectResponse {
        $factura = Factura::insertar(
            $request->validated()
        );

        Bitacora::registrar(
            Bitacora::CREADO,
            'FACTURAS',
            $factura->getKey(),
            'Se registró una nueva facturacion.'
        );

        return redirect()
            ->route('facturas')
            ->with('success', 'La facturacion se registró correctamente.');
    }

    public function buscarFactura(int $id): JsonResponse
    {
        $factura = Factura::buscarFacturaXId($id);

        if (!$factura) {
            return response()->json([
                'mensaje' => 'La facturacion no existe.',
            ], 404);
        }

        return response()->json($factura);
    }
}
