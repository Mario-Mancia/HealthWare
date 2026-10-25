<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Notificacion;
use App\Http\Requests\InsertarNotificacionRequest;
use App\Http\Requests\ActualizarNotificacionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificacionController extends Controller
{
    // Mostrar vista principal
    public function index(): View
    {
        return view('notificacion.notificaciones', [
            'notificaciones' => Notificacion::mostrarTodos()
        ]);
    }

    // Obtener todas las notificaciones en JSON
    public function mostrarTodos(): JsonResponse
    {
        return response()->json(Notificacion::mostrarTodos());
    }

    // Insertar notificación
    public function insertar(InsertarNotificacionRequest $request): RedirectResponse
    {
        $notificacion = Notificacion::insertar($request->validated());

        Bitacora::registrar(
            Bitacora::CREADO,
            'NOTIFICACIONES',
            $notificacion->getKey(),
            'Se registró una nueva notificación.'
        );

        return redirect()
            ->route('notificaciones')
            ->with('success', 'Notificación registrada correctamente.');
    }

    // Buscar notificación por ID
    public function buscar(int $id): JsonResponse
    {
        $notificacion = Notificacion::buscarNotificacionXId($id);

        if (!$notificacion) {
            return response()->json([
                'mensaje' => 'Notificación no encontrada.'
            ], 404);
        }

        return response()->json($notificacion);
    }

    // Actualizar notificación
    public function actualizar(
        ActualizarNotificacionRequest $request,
        int $id
    ): RedirectResponse {
        $notificacion = Notificacion::find($id);

        if (!$notificacion) {
            return redirect()
                ->route('notificaciones')
                ->with('error', 'Notificación no encontrada.');
        }

        $notificacion->actualizar($request->validated());

        Bitacora::registrar(
            Bitacora::ACTUALIZADO,
            'NOTIFICACIONES',
            $notificacion->getKey(),
            'Se actualizó una notificación.'
        );

        return redirect()
            ->route('notificaciones')
            ->with('success', 'Notificación actualizada correctamente.');
    }
}
