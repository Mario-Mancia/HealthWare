<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActualizarDoctorRequest;
use App\Http\Requests\InsertarDoctorRequest;
use App\Models\Bitacora;
use App\Models\Doctor;
use App\Models\Especialidad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DoctorController extends Controller
{
    /**
     * Muestra los doctores registrados.
     */
    public function index(): View
    {
        $datos = [
            'doctores' => Doctor::mostrarTodos(),
            'especialidades' => Especialidad::mostrarTodos(),
        ];

        return view('doctor.doctores', $datos);
    }

    /**
     * Inserta el registro profesional de un usuario.
     */
    public function insertarDoctor(
        InsertarDoctorRequest $request
    ): RedirectResponse {
        $doctor = Doctor::insertar($request->validated());

        Bitacora::registrar(
            Bitacora::CREADO,
            'DOCTORES',
            $doctor->getKey(),
            'Registro profesional de doctor creado correctamente. '
            . 'Usuario ID: ' . $doctor->usuario_id
            . ', licencia: ' . $doctor->numero_licencia
        );

        return redirect()
            ->route('doctores')
            ->with(
                'success',
                'Doctor registrado correctamente.'
            );
    }

    /**
     * Busca un doctor por su ID.
     */
    public function buscarDoctorXId(int $id): JsonResponse
    {
        $doctor = Doctor::buscarDoctorXId($id);

        if (!$doctor) {
            return response()->json(
                ['message' => 'Doctor no encontrado.'],
                404
            );
        }

        return response()->json($doctor);
    }

    /**
     * Actualiza los datos profesionales de un doctor.
     */
    public function actualizarDoctor(
        ActualizarDoctorRequest $request,
        Doctor $doctor
    ): RedirectResponse {
        if (
            Doctor::actualizar(
                $request->validated(),
                $doctor->getKey()
            )
        ) {
            Bitacora::registrar(
                Bitacora::ACTUALIZADO,
                'DOCTORES',
                $doctor->getKey(),
                'Registro profesional de doctor actualizado. '
                . 'Usuario ID: ' . $doctor->usuario_id
                . ', licencia: ' . $doctor->numero_licencia
            );

            return redirect()
                ->route('doctores')
                ->with(
                    'success',
                    'Doctor actualizado correctamente.'
                );
        }

        return redirect()
            ->route('doctores')
            ->with(
                'error',
                'El doctor no pudo ser actualizado.'
            );
    }
}
