<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
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

    public const ESTADO_ELIMINADO = 3;

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

            'contrasenha' => $id === null
                ? [
                    'required',
                    'string',
                    'min:8',
                    'max:250',
                    'confirmed',
                ]
                : [
                    'nullable',
                    'string',
                    'min:8',
                    'max:250',
                    'confirmed',
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
        return static::with(['rol', 'estado'])->get();
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

        if ($usuario->id_estado === self::ESTADO_ELIMINADO) {
            return false;
        }

        /*
         * Si no se proporciona una nueva contraseña,
         * se conserva la contraseña actual.
         */
        if (
            array_key_exists('contrasenha', $datos) &&
            blank($datos['contrasenha'])
        ) {
            unset($datos['contrasenha']);
        }

        return $usuario->update($datos);
    }

    /**
     * Elimina de forma lógica un usuario existente.
     *
     * No elimina físicamente el registro por motivos de auditoría, simplemente cambia su estado a eliminado
     * y ya no se pueden realizar más acciones con él, incluso revertirlo, al menos desde el sistema.
     */
    public static function eliminar(int $id): bool
    {
        $usuario = static::find($id);

        if (!$usuario) {
            return false;
        }

        if ($usuario->id_estado === self::ESTADO_ELIMINADO) {
            return false;
        }

        return $usuario->update(['id_estado' => self::ESTADO_ELIMINADO]);
    }

    public static function estadosDeUsuario()
    {

        return \DB::table('vista_usuarios_por_estado')->select('nombre_estado', 'total_usuarios')->get();
    }
}
