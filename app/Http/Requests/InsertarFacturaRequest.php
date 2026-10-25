<?php

namespace App\Http\Requests;

use App\Models\Factura;
use Illuminate\Foundation\Http\FormRequest;

class InsertarFacturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return Factura::validaciones();
    }

    public function messages(): array
    {
        return [
            'id_paciente.required' => 'Debe seleccionar un paciente.',
            'id_paciente.integer' => 'El paciente seleccionado no es válido.',
            'id_paciente.exists' => 'El paciente seleccionado no existe.',

            'numero_factura.required' => 'Debe ingresar el número de factura.',
            'numero_factura.string' => 'El número de factura debe ser texto.',
            'numero_factura.max' => 'El número de factura no debe superar los 50 caracteres.',
            'numero_factura.unique' => 'El número de factura ya está registrado.',

            'fecha_emision.date' => 'La fecha de emisión no es válida.',

            'estado.in' => 'El estado de la factura seleccionado no es válido.',

            'subtotal.numeric' => 'El subtotal debe ser un valor numérico.',
            'subtotal.min' => 'El subtotal no puede ser negativo.',
            'subtotal.max' => 'El subtotal supera el límite permitido.',

            'impuesto.numeric' => 'El impuesto debe ser un valor numérico.',
            'impuesto.min' => 'El impuesto no puede ser negativo.',
            'impuesto.max' => 'El impuesto supera el límite permitido.',

            'descuento.numeric' => 'El descuento debe ser un valor numérico.',
            'descuento.min' => 'El descuento no puede ser negativo.',
            'descuento.max' => 'El descuento supera el límite permitido.',

            'total.numeric' => 'El total debe ser un valor numérico.',
            'total.min' => 'El total no puede ser negativo.',
            'total.max' => 'El total supera el límite permitido.',

            'notas.string' => 'Las notas deben ser texto.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id_paciente' => 'paciente',
            'numero_factura' => 'número de factura',
            'fecha_emision' => 'fecha de emisión',
            'estado' => 'estado',
            'subtotal' => 'subtotal',
            'impuesto' => 'impuesto',
            'descuento' => 'descuento',
            'total' => 'total',
            'notas' => 'notas',
        ];
    }
}
