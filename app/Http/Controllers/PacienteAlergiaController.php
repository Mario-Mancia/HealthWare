<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsertarPacienteAlergiaRequest;
use App\Models\Bitacora;
use App\Models\PacienteAlergia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PacienteAlergiaController extends Controller
{
    public function index(): View
    {
        return view('pacientes_alergias.index', [
            'pacientesAlergias' => PacienteAlergia::mostrarTodos(),
        ]);
    }

    public function insertarPacienteAlergia(
        InsertarPacienteAlergiaRequest $request
    ): RedirectResponse {
        $pacienteAlergia = PacienteAlergia::insertar(
            $request->validated()
        );

        Bitacora::registrar(
            Bitacora::CREADO,
            'PACIENTES_ALERGIAS',
            $pacienteAlergia->getKey(),
            'Se registró una alergia para un paciente.'
        );

        return redirect()
            ->route('pacientes_alergias')
            ->with('success', 'La alergia se registró correctamente.');
    }

    public function buscarPacienteAlergia(int $id): JsonResponse
    {
        $pacienteAlergia =
            PacienteAlergia::buscarPacienteAlergiaXId($id);

        if (!$pacienteAlergia) {
            return response()->json([
                'mensaje' => 'El registro de alergia no existe.',
            ], 404);
        }

        return response()->json($pacienteAlergia);
    }

    public function eliminarPacienteAlergia(
        PacienteAlergia $pacienteAlergia
    ): RedirectResponse {
        $id = $pacienteAlergia->getKey();

        PacienteAlergia::eliminar($id);

        Bitacora::registrar(
            Bitacora::ELIMINADO,
            'PACIENTES_ALERGIAS',
            $id,
            'Se eliminó una alergia registrada para un paciente.'
        );

        return redirect()
            ->route('pacientes_alergias')
            ->with('success', 'La alergia se eliminó correctamente.');
    }
}
