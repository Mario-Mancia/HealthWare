<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsertarCitaRequest;
use App\Http\Requests\ActualizarCitaRequest;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Doctor;
use App\Models\Bitacora;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CitaController extends Controller
{
    public function index(): View
    {
        return view('cita.citas', [
            'citas' => Cita::mostrarTodos(),
            'pacientes' => Paciente::mostrarTodos(),
            'doctores' => Doctor::mostrarTodos(),
        ]);
    }

    public function insertarCita(
        InsertarCitaRequest $request
    ): RedirectResponse {
        $cita = Cita::insertar($request->validated());

        Bitacora::registrar(
            Bitacora::CREADO,
            'CITAS',
            $cita->getKey(),
            'Cita registrada correctamente. '
            . 'Paciente ID: ' . $cita->id_paciente
            . ', doctor ID: ' . $cita->id_doctor
            . ', fecha: ' . $cita->fecha_cita->format('Y-m-d')
            . ', hora: ' . $cita->hora_inicio
        );

        return redirect()
            ->route('citas')
            ->with('success', 'Cita registrada correctamente.');
    }

    public function buscarCitaXId(int $id): JsonResponse
    {
        $cita = Cita::buscarCitaXId($id);

        if (!$cita) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró la cita solicitada.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'cita' => $cita,
        ]);
    }

    public function actualizarCita(
        ActualizarCitaRequest $request,
        Cita $cita
    ): RedirectResponse {
        $actualizada = Cita::actualizar(
            $request->validated(),
            $cita->getKey()
        );

        if ($actualizada) {
            Bitacora::registrar(
                Bitacora::ACTUALIZADO,
                'CITAS',
                $cita->getKey(),
                'Cita actualizada correctamente. '
                . 'Paciente ID: ' . $cita->id_paciente
                . ', doctor ID: ' . $cita->id_doctor
                . ', fecha: ' . $cita->fecha_cita->format('Y-m-d')
                . ', hora: ' . $cita->hora_inicio
            );

            return redirect()
                ->route('citas')
                ->with('success', 'Cita actualizada correctamente.');
        }

        return redirect()
            ->route('citas')
            ->with('error', 'No fue posible actualizar la cita.');
    }

    public function eliminarCita(Cita $cita): RedirectResponse
    {
        $id = $cita->getKey();
        $fecha = $cita->fecha_cita->format('Y-m-d');
        $hora = $cita->hora_inicio;

        if (Cita::eliminar($id)) {
            Bitacora::registrar(
                Bitacora::ELIMINADO,
                'CITAS',
                $id,
                'Cita eliminada. Fecha: ' . $fecha
                . ', hora: ' . $hora
            );

            return redirect()
                ->route('citas')
                ->with('success', 'Cita eliminada correctamente.');
        }

        return redirect()
            ->route('citas')
            ->with('error', 'No fue posible eliminar la cita.');
    }
}
