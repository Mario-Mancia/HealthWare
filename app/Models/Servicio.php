<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';

    protected $fillable = [
        'servicio',
        'descripcion',
    ];

    /**
     * Reglas de validación para el modelo.
     */
    private static function validaciones($id = null): array
    {
        return [
            'servicio' => [
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
     * Obtiene todos los servicios registrados.
     */
    public static function mostrarTodos(): Collection
    {
        return static::all();
    }

}
