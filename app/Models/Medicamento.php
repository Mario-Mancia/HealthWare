<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class Medicamento extends Model
{
    use HasFactory;

    protected $table = 'medicamentos';

    protected $fillable = [
        'nombre',
        'principio_activo',
        'presentacion',
        'concentracion',
        'descripcion',
    ];

    /**
     * Reglas de validación para el modelo.
     */
    private static function validaciones($id = null): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:50',
            ],
            'principio_activo' => [
                'nullable',
                'string',
                'max:150',
            ],
            'presentacion' => [
                'nullable',
                'string',
                'max:100',
            ],
            'concentracion' => [
                'nullable',
                'string',
                'max:100',
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:150',
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
     * Obtiene todos los medicamentos registrados.
     */
    public static function mostrarTodos(): Collection
    {
        return static::all();
    }
}
