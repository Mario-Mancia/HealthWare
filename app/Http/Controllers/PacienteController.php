<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\TipoSanguineo;
use App\Models\Bitacora;
use App\Http\Requests\InsertarPacienteRequest;
use App\Http\Requests\ActualizarPacienteRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PacienteController extends Controller
{
    /**
     * Muestra los pacientes registrados.
     */
    public function index(): View
    {
        $datos = [
            'pacientes' => Paciente::mostrarTodos(),
            'tiposSanguineos' => TipoSanguineo::all(),
        ];

        return view('paciente.pacientes', $datos);
    }

    /**
     * Inserta un nuevo paciente.
     */
    public function insertarPaciente(
        InsertarPacienteRequest $request
    ): RedirectResponse {
        $paciente = Paciente::insertar(
            $request->validated()
        );

        Bitacora::registrar(
            Bitacora::CREADO,
            'PACIENTES',
            $paciente->getKey(),
            'Paciente registrado correctamente. '
            . 'Identificación: '
            . $paciente->numero_identificacion
            . ', nombre: '
            . $paciente->nombres
            . ' '
            . $paciente->apellidos
        );

        return redirect()
            ->route('pacientes')
            ->with(
                'success',
                'Paciente registrado correctamente.'
            );
    }

    /**
     * Busca un paciente por su ID.
     */
    public function buscarPacienteXId(
        int $id
    ): JsonResponse {
        $paciente = Paciente::buscarPacienteXId($id);

        if (!$paciente) {
            return response()->json(
                ['message' => 'Paciente no encontrado.'],
                404
            );
        }

        return response()->json($paciente);
    }

    /**
     * Actualiza los datos de un paciente.
     */
    public function actualizarPaciente(
        ActualizarPacienteRequest $request,
        Paciente $paciente
    ): RedirectResponse {
        if (
            Paciente::actualizar(
                $request->validated(),
                $paciente->getKey()
            )
        ) {
            Bitacora::registrar(
                Bitacora::ACTUALIZADO,
                'PACIENTES',
                $paciente->getKey(),
                'Datos del paciente actualizados. '
                . 'Identificación: '
                . $paciente->numero_identificacion
                . ', nombre: '
                . $paciente->nombres
                . ' '
                . $paciente->apellidos
            );

            return redirect()
                ->route('pacientes')
                ->with(
                    'success',
                    'Paciente actualizado correctamente.'
                );
        }

        return redirect()
            ->route('pacientes')
            ->with(
                'error',
                'El paciente no pudo ser actualizado.'
            );
    }

    /**
     * Elimina un paciente.
     */
    public function eliminarPaciente(
        Paciente $paciente
    ): RedirectResponse {
        // Guardamos los datos necesarios antes de eliminar.
        $id = $paciente->getKey();
        $identificacion = $paciente->numero_identificacion;
        $nombre = $paciente->nombres . ' ' . $paciente->apellidos;

        if (Paciente::eliminar($id)) {
            Bitacora::registrar(
                Bitacora::ELIMINADO,
                'PACIENTES',
                $id,
                'Paciente eliminado. '
                . 'Identificación: '
                . $identificacion
                . ', nombre: '
                . $nombre
            );

            return redirect()
                ->route('pacientes')
                ->with(
                    'success',
                    'Paciente eliminado correctamente.'
                );
        }

        return redirect()
            ->route('pacientes')
            ->with(
                'error',
                'El paciente no pudo ser eliminado.'
            );
    }
}
