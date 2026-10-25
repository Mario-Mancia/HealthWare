<?php

namespace App\Http\Requests;

use App\Models\DetalleFactura;
use Illuminate\Foundation\Http\FormRequest;

class InsertarDetalleFacturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return DetalleFactura::validaciones();
    }

    public function messages(): array
    {
        return [
            'id_factura.required' => 'Debe seleccionar una factura.',
            'id_factura.integer' => 'La factura seleccionada no es válida.',
            'id_factura.exists' => 'La factura seleccionada no existe.',

            'id_servicio.required' => 'Debe seleccionar un servicio.',
            'id_servicio.integer' => 'El servicio seleccionado no es válido.',
            'id_servicio.exists' => 'El servicio seleccionado no existe.',

            'descripcion.string' => 'La descripción debe ser texto.',

            'cantidad.numeric' => 'La cantidad debe ser un valor numérico.',
            'cantidad.gt' => 'La cantidad debe ser mayor que cero.',
            'cantidad.max' => 'La cantidad supera el límite permitido.',

            'precio_unitario.required' => 'Debe ingresar el precio unitario.',
            'precio_unitario.numeric' => 'El precio unitario debe ser numérico.',
            'precio_unitario.min' => 'El precio unitario no puede ser negativo.',
            'precio_unitario.max' => 'El precio unitario supera el límite permitido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id_factura' => 'factura',
            'id_servicio' => 'servicio',
            'descripcion' => 'descripción',
            'cantidad' => 'cantidad',
            'precio_unitario' => 'precio unitario',
        ];
    }
}
