<?php

namespace App\Http\Requests;

use App\Models\RegistroMedico;
use Illuminate\Foundation\Http\FormRequest;

class InsertarRegistroMedicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return RegistroMedico::validaciones();
    }

    public function messages(): array
    {
        return [
            'id_paciente.required' => 'Debe seleccionar un paciente.',
            'id_paciente.integer' => 'El paciente seleccionado no es válido.',
            'id_paciente.exists' => 'El paciente seleccionado no existe.',

            'id_doctor.required' => 'Debe seleccionar un doctor.',
            'id_doctor.integer' => 'El doctor seleccionado no es válido.',
            'id_doctor.exists' => 'El doctor seleccionado no existe.',

            'id_cita.integer' => 'La cita seleccionada no es válida.',
            'id_cita.exists' => 'La cita seleccionada no existe.',

            'fecha_registro.required' => 'La fecha del registro médico es obligatoria.',
            'fecha_registro.date' => 'Debe ingresar una fecha y hora válidas.',

            'motivo_consulta.string' => 'El motivo de consulta debe ser texto válido.',
            'notas_clinicas.string' => 'Las notas clínicas deben ser texto válido.',
            'observaciones.string' => 'Las observaciones deben ser texto válido.',

            'signos_vitales.array' => 'Los signos vitales enviados no tienen un formato válido.',

            'signos_vitales.peso.numeric' => 'El peso debe ser numérico.',
            'signos_vitales.peso.gt' => 'El peso debe ser mayor que cero.',
            'signos_vitales.peso.max' => 'El peso supera el valor permitido.',

            'signos_vitales.altura.numeric' => 'La altura debe ser numérica.',
            'signos_vitales.altura.gt' => 'La altura debe ser mayor que cero.',
            'signos_vitales.altura.max' => 'La altura supera el valor permitido.',

            'signos_vitales.temperatura.numeric' => 'La temperatura debe ser numérica.',
            'signos_vitales.temperatura.between' => 'La temperatura debe estar entre 20 y 50 °C.',

            'signos_vitales.frecuencia_cardiaca.integer' => 'La frecuencia cardíaca debe ser un número entero.',
            'signos_vitales.frecuencia_cardiaca.min' => 'La frecuencia cardíaca debe ser mayor que cero.',
            'signos_vitales.frecuencia_respiratoria.integer' => 'La frecuencia respiratoria debe ser un número entero.',
            'signos_vitales.frecuencia_respiratoria.min' => 'La frecuencia respiratoria debe ser mayor que cero.',

            'signos_vitales.saturacion_oxigeno.numeric' => 'La saturación de oxígeno debe ser numérica.',
            'signos_vitales.saturacion_oxigeno.between' => 'La saturación de oxígeno debe estar entre 0 y 100.',

            'signos_vitales.presion_sistolica.integer' => 'La presión sistólica debe ser un número entero.',
            'signos_vitales.presion_sistolica.min' => 'La presión sistólica debe ser mayor que cero.',
            'signos_vitales.presion_diastolica.integer' => 'La presión diastólica debe ser un número entero.',
            'signos_vitales.presion_diastolica.min' => 'La presión diastólica debe ser mayor que cero.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id_paciente' => 'paciente',
            'id_doctor' => 'doctor',
            'id_cita' => 'cita médica',
            'fecha_registro' => 'fecha del registro',
            'motivo_consulta' => 'motivo de consulta',
            'notas_clinicas' => 'notas clínicas',
            'observaciones' => 'observaciones',
        ];
    }
}
