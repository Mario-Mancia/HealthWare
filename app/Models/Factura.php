<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Validator;

class Factura extends Model
{
    use HasFactory;

    protected $table = 'facturas';

    protected $fillable = [
        'id_paciente',
        'numero_factura',
        'fecha_emision',
        'estado',
        'subtotal',
        'impuesto',
        'descuento',
        'total',
        'notas',
    ];

    protected $casts = [
        'id_paciente' => 'integer',
        'fecha_emision' => 'datetime',
        'subtotal' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'id_paciente');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleFactura::class, 'id_factura');
    }

    public static function validaciones(): array
    {
        return [
            'id_paciente' => [
                'required',
                'integer',
                'exists:pacientes,id',
            ],
            'numero_factura' => [
                'required',
                'string',
                'max:50',
                'unique:facturas,numero_factura',
            ],
            'fecha_emision' => [
                'sometimes',
                'date',
            ],
            'estado' => [
                'sometimes',
                'in:pendiente,pago_parcial,pagada,cancelada',
            ],
            'subtotal' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],
            'impuesto' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],
            'descuento' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],
            'total' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],
            'notas' => [
                'nullable',
                'string',
            ],
        ];
    }

    public static function rules(array $datos): array
    {
        return Validator::make(
            $datos,
            static::validaciones()
        )->validate();
    }

    public static function mostrarTodos(): Collection
    {
        return static::with('paciente')
            ->orderByDesc('fecha_emision')
            ->get();
    }

    public static function buscarFacturaXId(int $id): ?Factura
    {
        return static::with('paciente')->find($id);
    }

    public static function insertar(array $datos): Factura
    {
        $datos = static::rules($datos);

        return static::create($datos);
    }
}
