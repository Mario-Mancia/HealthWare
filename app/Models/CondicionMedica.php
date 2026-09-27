<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class CondicionMedica extends Model
{
    use HasFactory;

    protected $table = 'condiciones_medicas';

    protected $fillable = [
        'condicion_medica',
        'descripcion',
    ];

    /**
     * Reglas de validación para el modelo.
     */
    private static function validaciones($id = null): array
    {
        return [
            'condicion_medica' => [
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
     * Obtiene todas las condiciones médicas registradas.
     */
    public static function mostrarTodos(): Collection
    {
        return static::all();
    }
}
