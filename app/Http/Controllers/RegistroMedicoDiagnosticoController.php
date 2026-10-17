<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsertarRegistroMedicoDiagnosticoRequest;
use App\Models\RegistroMedicoDiagnostico;
use App\Models\RegistroMedico;
use App\Models\Diagnostico;
use App\Models\Bitacora;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegistroMedicoDiagnosticoController extends Controller
{
    public function index(): View
    {
        return view(
            'registro_medico_diagnostico.registro_medico_diagnosticos',
            [
                'registrosMedicosDiagnosticos' =>
                    RegistroMedicoDiagnostico::mostrarTodos(),

                'registrosMedicos' =>
                    RegistroMedico::mostrarTodos(),

                'diagnosticos' =>
                    Diagnostico::mostrarTodos(),
            ]
        );
    }

    public function insertarRegistroMedicoDiagnostico(
        InsertarRegistroMedicoDiagnosticoRequest $request
    ): RedirectResponse {
        $registro = RegistroMedicoDiagnostico::insertar(
            $request->validated()
        );

        Bitacora::registrar(
            Bitacora::CREADO,
            'REG_MED_DIA',
            $registro->getKey(),
            'Diagnóstico asociado al registro médico. '
            . 'Registro médico ID: ' . $registro->id_registro_medico
            . ', diagnóstico ID: ' . $registro->id_diagnostico
        );

        return redirect()
            ->route('registro_medico_diagnosticos')
            ->with(
                'success',
                'Diagnóstico asociado al registro médico correctamente.'
            );
    }

    public function buscarRegistroMedicoDiagnosticoXId(
        int $id
    ): JsonResponse {
        $registro = RegistroMedicoDiagnostico::
        buscarRegistroMedicoDiagnosticoXId($id);

        if (!$registro) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró la asociación solicitada.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'registro' => $registro,
        ]);
    }

    public function eliminarRegistroMedicoDiagnostico(
        RegistroMedicoDiagnostico $registroMedicoDiagnostico
    ): RedirectResponse {
        $id = $registroMedicoDiagnostico->getKey();
        $idRegistroMedico =
            $registroMedicoDiagnostico->id_registro_medico;
        $idDiagnostico =
            $registroMedicoDiagnostico->id_diagnostico;

        if (RegistroMedicoDiagnostico::eliminar($id)) {
            Bitacora::registrar(
                Bitacora::ELIMINADO,
                'REG_MED_DIA',
                $id,
                'Asociación de diagnóstico eliminada. '
                . 'Registro médico ID: ' . $idRegistroMedico
                . ', diagnóstico ID: ' . $idDiagnostico
            );

            return redirect()
                ->route('registro_medico_diagnosticos')
                ->with(
                    'success',
                    'Asociación eliminada correctamente.'
                );
        }

        return redirect()
            ->route('registro_medico_diagnosticos')
            ->with(
                'error',
                'No fue posible eliminar la asociación.'
            );
    }
}
