<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Usuario extends Authenticatable
{
    use HasFactory;

    protected $table = 'usuarios';

    protected $fillable = [
        'id_rol',
        'id_estado',
        'nombres',
        'apellidos',
        'correo',
        'contrasenha',
        'telefono',
    ];

    protected $hidden = [
        'contrasenha'
    ];

    protected $casts = [
        'id_rol' => 'integer',
        'id_estado' => 'integer',
        'contrasenha' => 'hashed',
    ];

    /**
     * Indica el nombre de la columna utilizada para la contraseña.
     */
    public function getAuthPasswordName(): string
    {
        return 'contrasenha';
    }

    /**
     * Relación con el rol del usuario.
     */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'id_rol');
    }

    /**
     * Relación con el estado del usuario.
     */
    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'id_estado');
    }

    /**
     * Reglas de validación para el modelo.
     */
    public static function validaciones($id = null): array
    {
        return [
            'id_rol' => [
                'required',
                'integer',
                'exists:roles,id',
            ],
            'id_estado' => [
                'required',
                'integer',
                'exists:estados,id',
            ],
            'nombres' => [
                'required',
                'string',
                'max:100',
            ],
            'apellidos' => [
                'required',
                'string',
                'max:100',
            ],
            'correo' => [
                'required',
                'string',
                'email',
                'max:150',
                Rule::unique('usuarios', 'correo')->ignore($id),
            ],
            'contrasenha' => [
                'required',
                'string',
                'min:8',
                'max:250',
            ],
            'telefono' => [
                'nullable',
                'string',
                'max:25',
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

    public static function loginRules(): array
    {
        return [
            'correo' => [
                'required',
                'string',
                'email',
                'max:150',
            ],
            'contrasenha' => [
                'required',
                'string',
            ],
        ];
    }

    /**
     * Obtiene todos los usuarios registrados.
     */
    public static function mostrarTodos(): Collection
    {
        return static::all();
    }

    /**
     * Inserta un nuevo usuario.
     */
    public static function insertar(array $datos): Usuario
    {
        return static::create($datos);
    }

    /**
     * Busca un usuario por su ID.
     */
    public static function buscarUsuarioXId(int $id): ?Usuario
    {
        return static::find($id);
    }

    /**
     * Actualiza un usuario existente.
     */
    public static function actualizar(array $datos, int $id): bool
    {
        $usuario = static::find($id);

        if (!$usuario) {
            return false;
        }

        return $usuario->update($datos);
    }

    /**
     * Elimina un usuario existente.
     */
    public static function eliminar(int $id): bool
    {
        $usuario = static::find($id);

        if (!$usuario) {
            return false;
        }

        return (bool)$usuario->delete();
    }
}
