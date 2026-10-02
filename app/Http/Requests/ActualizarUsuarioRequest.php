<?php

namespace App\Http\Requests;

use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;

class ActualizarUsuarioRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado
     * para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación.
     */
    public function rules(): array
    {
        /** @var Usuario $usuario */
        $usuario = $this->route('usuario');

        return Usuario::validaciones(
            $usuario->getKey()
        );
    }

    /**
     * Mensajes personalizados de validación.
     */
    public function messages(): array
    {
        return [
            'id_rol.required' => 'El rol es obligatorio.',
            'id_rol.integer' => 'El rol seleccionado no es válido.',
            'id_rol.exists' => 'El rol seleccionado no existe.',

            'id_estado.required' => 'El estado es obligatorio.',
            'id_estado.integer' => 'El estado seleccionado no es válido.',
            'id_estado.exists' => 'El estado seleccionado no existe.',

            'nombres.required' => 'Los nombres son obligatorios.',
            'nombres.string' => 'Los nombres deben ser texto.',
            'nombres.max' => 'Los nombres no deben exceder los 100 caracteres.',

            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.string' => 'Los apellidos deben ser texto.',
            'apellidos.max' => 'Los apellidos no deben exceder los 100 caracteres.',

            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.string' => 'El correo electrónico debe ser texto.',
            'correo.email' => 'Debes ingresar una dirección de correo válida.',
            'correo.max' => 'El correo no debe exceder los 150 caracteres.',
            'correo.unique' => 'Este correo electrónico ya está en uso por otro usuario.',

            'contrasenha.required' => 'La contraseña es obligatoria.',
            'contrasenha.string' => 'La contraseña debe ser texto.',
            'contrasenha.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'contrasenha.max' => 'La contraseña no debe exceder los 250 caracteres.',

            'telefono.string' => 'El teléfono debe ser texto.',
            'telefono.max' => 'El teléfono no debe exceder los 25 caracteres.',
        ];
    }
}
