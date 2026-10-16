<?php

namespace App\Http\Controllers;

use App\Models\HorarioDoctor;
use App\Models\Doctor;
use App\Models\Bitacora;
use App\Http\Requests\ActualizarHorariosRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class HorarioDoctorController extends Controller
{
    /**
     * Muestra los horarios registrados.
     */
    public function index(): View
    {
        $datos = [
            'horarios' => HorarioDoctor::mostrarTodos(),
            'doctores' => Doctor::mostrarTodos(),
        ];

        return view('horarios.horarios', $datos);
    }

    /**
     * Inserta un nuevo horario para un doctor.
     */
    public function insertarHorario(
        Request $request
    ): RedirectResponse {
        $horario = HorarioDoctor::insertar(
            $request->all()
        );

        Bitacora::registrar(
            Bitacora::CREADO,
            'HORARIOS_DOCTORES',
            $horario->getKey(),
            'Horario de doctor creado correctamente. '
            . 'Doctor ID: ' . $horario->id_doctor
            . ', día: ' . $horario->dia_semana
            . ', desde: ' . $horario->hora_inicio
            . ', hasta: ' . $horario->hora_fin
        );

        return redirect()
            ->route('horarios')
            ->with(
                'success',
                'Horario registrado correctamente.'
            );
    }

    /**
     * Busca un horario por su ID.
     */
    public function buscarHorarioXId(
        int $id
    ): JsonResponse {
        $horario = HorarioDoctor::buscarHorarioXId($id);

        if (!$horario) {
            return response()->json(
                ['message' => 'Horario no encontrado.'],
                404
            );
        }

        return response()->json($horario);
    }

    /**
     * Actualiza un horario existente.
     */
    public function actualizarHorario(
        ActualizarHorariosRequest $request,
        HorarioDoctor $horario
    ): RedirectResponse {
        if (
            HorarioDoctor::actualizar(
                $request->validated(),
                $horario->getKey()
            )
        ) {
            Bitacora::registrar(
                Bitacora::ACTUALIZADO,
                'HORARIOS_DOCTORES',
                $horario->getKey(),
                'Horario de doctor actualizado. '
                . 'Doctor ID: ' . $horario->id_doctor
                . ', día: ' . $horario->dia_semana
                . ', desde: ' . $horario->hora_inicio
                . ', hasta: ' . $horario->hora_fin
            );

            return redirect()
                ->route('horarios')
                ->with(
                    'success',
                    'Horario actualizado correctamente.'
                );
        }

        return redirect()
            ->route('horarios')
            ->with(
                'error',
                'El horario no pudo ser actualizado.'
            );
    }

    /**
     * Elimina un horario existente.
     */
    public function eliminarHorario(
        HorarioDoctor $horario
    ): RedirectResponse {
        // Guardamos los datos antes de eliminar el registro.
        $id = $horario->getKey();
        $idDoctor = $horario->id_doctor;
        $dia = $horario->dia_semana;

        if (HorarioDoctor::eliminar($id)) {
            Bitacora::registrar(
                Bitacora::ELIMINADO,
                'HORARIOS_DOCTORES',
                $id,
                'Horario de doctor eliminado. '
                . 'Doctor ID: ' . $idDoctor
                . ', día: ' . $dia
            );

            return redirect()
                ->route('horarios')
                ->with(
                    'success',
                    'Horario eliminado correctamente.'
                );
        }

        return redirect()
            ->route('horarios')
            ->with(
                'error',
                'El horario no pudo ser eliminado.'
            );
    }
}
