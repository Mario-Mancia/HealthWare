<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsertarRegistroMedicoRequest;
use App\Models\RegistroMedico;
use App\Models\Paciente;
use App\Models\Doctor;
use App\Models\Cita;
use App\Models\Bitacora;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegistroMedicoController extends Controller
{
    public function index(): View
    {
        return view('registro_medico.registros_medicos', [
            'registrosMedicos' => RegistroMedico::mostrarTodos(),
            'pacientes' => Paciente::mostrarTodos(),
            'doctores' => Doctor::mostrarTodos(),
            'citas' => Cita::mostrarTodos(),
        ]);
    }

    public function insertarRegistroMedico(
        InsertarRegistroMedicoRequest $request
    ): RedirectResponse {
        $registroMedico = RegistroMedico::insertar(
            $request->validated()
        );

        Bitacora::registrar(
            Bitacora::CREADO,
            'REGISTROS_MEDICOS',
            $registroMedico->getKey(),
            'Registro médico creado correctamente. '
            . 'Paciente ID: ' . $registroMedico->id_paciente
            . ', doctor ID: ' . $registroMedico->id_doctor
            . ', cita ID: '
            . ($registroMedico->id_cita ?? 'Sin cita asociada')
        );

        return redirect()
            ->route('registros_medicos')
            ->with(
                'success',
                'Registro médico creado correctamente.'
            );
    }

    public function buscarRegistroMedicoXId(
        int $id
    ): JsonResponse {
        $registroMedico =
            RegistroMedico::buscarRegistroMedicoXId($id);

        if (!$registroMedico) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró el registro médico solicitado.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'registroMedico' => $registroMedico,
        ]);
    }

    public function eliminarRegistroMedico(
        RegistroMedico $registroMedico
    ): RedirectResponse {
        $id = $registroMedico->getKey();
        $idPaciente = $registroMedico->id_paciente;
        $idDoctor = $registroMedico->id_doctor;

        if (RegistroMedico::eliminar($id)) {
            Bitacora::registrar(
                Bitacora::ELIMINADO,
                'REGISTROS_MEDICOS',
                $id,
                'Registro médico eliminado. '
                . 'Paciente ID: ' . $idPaciente
                . ', doctor ID: ' . $idDoctor
            );

            return redirect()
                ->route('registros_medicos')
                ->with(
                    'success',
                    'Registro médico eliminado correctamente.'
                );
        }

        return redirect()
            ->route('registros_medicos')
            ->with(
                'error',
                'No fue posible eliminar el registro médico.'
            );
    }
}
