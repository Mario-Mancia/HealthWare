<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;


/***
 * Diagnosticos tiene una función diferente a CondicionesMedicas, esta es una tabla que va relacionada con las
 * citas médicas específicas, mientras que CondicionesMedicas va relacionada con el historial médico de un paciente.
 */
class Diagnostico extends Model
{
    use HasFactory;

    protected $table = 'diagnosticos';

    protected $fillable = [
        'diagnostico',
        'descripcion',
    ];

    /**
     * Reglas de validación para el modelo.
     */
    private static function validaciones($id = null): array
    {
        return [
            'diagnostico' => [
                'required',
                'string',
                'max:50',
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:250',
            ],
        ];
    }

    /**
     * Valida los datos proporcionados.
     */
    public static function rules(array $datos, $id = null): array
    {
        return Validator::make(
            $datos,
            static::validaciones($id)
        )->validate();
    }

    /**
     * Obtiene todos los diagnósticos registrados.
     */
    public static function mostrarTodos(): Collection
    {
        return static::all();
    }
}
