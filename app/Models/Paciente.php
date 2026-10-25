<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
class Paciente extends Model
{
    use HasFactory;

    protected $table = 'pacientes';

    protected $fillable = [
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'genero',
        'id_tipo_sanguineo',
        'numero_identificacion',
        'telefono',
        'correo',
        'direccion',
        'nombre_contacto_emergencia',
        'telefono_contacto_emergencia',
        'parentesco_contacto_emergencia',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'id_tipo_sanguineo' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Obtiene el tipo sanguíneo del paciente.
     */
    public function tipoSanguineo(): BelongsTo
    {
        return $this->belongsTo(
            TipoSanguineo::class,
            'id_tipo_sanguineo'
        );
    }

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class, 'id_paciente');
    }

    /*
    |--------------------------------------------------------------------------
    | Validaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Define las reglas para insertar o actualizar pacientes.
     */
    public static function validaciones($id = null): array
    {
        return [
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

            'fecha_nacimiento' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'genero' => [
                'required',
                Rule::in([
                    'Masculino',
                    'Femenino'
                ]),
            ],

            'id_tipo_sanguineo' => [
                'nullable',
                'integer',
                'exists:tipos_sanguineos,id',
            ],

            'numero_identificacion' => [
                'required',
                'string',
                'max:10',
                Rule::unique(
                    'pacientes',
                    'numero_identificacion'
                )->ignore($id),
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:25',
            ],

            'correo' => [
                'nullable',
                'email',
                'max:150',
            ],

            'direccion' => [
                'nullable',
                'string',
            ],

            'nombre_contacto_emergencia' => [
                'nullable',
                'string',
                'max:150',
            ],

            'telefono_contacto_emergencia' => [
                'nullable',
                'string',
                'max:25',
            ],

            'parentesco_contacto_emergencia' => [
                'nullable',
                'string',
                'max:50',
            ],
        ];
    }

    /**
     * Valida los datos y devuelve los datos validados.
     */
    public static function rules(
        array $datos,
              $id = null
    ): array {
        return Validator::make(
            $datos,
            static::validaciones($id)
        )->validate();
    }

    /*
    |--------------------------------------------------------------------------
    | Consultas
    |--------------------------------------------------------------------------
    */

    /**
     * Obtiene todos los pacientes con su tipo sanguíneo.
     */
    public static function mostrarTodos(): Collection
    {
        return static::with('tipoSanguineo')
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();
    }

    /**
     * Busca un paciente por su identificador.
     */
    public static function buscarPacienteXId(
        int $id
    ): ?Paciente {
        return static::with('tipoSanguineo')
            ->find($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Operaciones CRUD
    |--------------------------------------------------------------------------
    */

    /**
     * Inserta un nuevo paciente.
     */
    public static function insertar(
        array $datos
    ): Paciente {
        $datosValidados = static::rules($datos);

        return static::create($datosValidados);
    }

    /**
     * Actualiza un paciente existente.
     */
    public static function actualizar(
        array $datos,
        int $id
    ): bool {
        $paciente = static::find($id);

        if (!$paciente) {
            return false;
        }

        $datosValidados = static::rules(
            $datos,
            $id
        );

        return $paciente->update($datosValidados);
    }

    /**
     * Elimina un paciente existente.
     */
    public static function eliminar(int $id): bool
    {
        $paciente = static::find($id);

        if (!$paciente) {
            return false;
        }

        return $paciente->delete();
    }
}
