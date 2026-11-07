@extends('layouts.app_layout')

@section('titulo', 'Factura')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/facturacion.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de facturas</h1>


    {{--
        VISTA: Gestión general de facturación.

        OBJETIVO:
        Centralizar la consulta y creación de facturas de los servicios
        proporcionados por la clínica.

        CONTENIDO:
        1. ENCABEZADO:
           - Título "Facturación".
           - Botón para generar una factura.

        2. INDICADORES:
           - Facturas emitidas.
           - Facturas pendientes.
           - Facturas pagadas.
           - Total facturado en el período.

        3. FILTROS:
           - Número de factura.
           - Paciente.
           - Fecha o rango de fechas.
           - Estado de la factura.

        4. TABLA:
           - Número o identificador de factura.
           - Paciente.
           - Fecha de emisión.
           - Total.
           - Estado.
           - Acciones.

        5. FORMULARIO / MODAL DE FACTURA:
           - Seleccionar paciente.
           - Fecha de emisión.
           - Agregar servicios.
           - Cantidad de cada servicio.
           - Precio unitario.
           - Subtotal.
           - Descuentos o impuestos, únicamente si están contemplados
             en las reglas de negocio.
           - Total calculado.

        6. ACCIONES:
           - Consultar detalle de factura.
           - Generar factura.
           - Consultar estado.
           - Acceder al proceso de pago cuando esté implementado.

        CONSIDERACIONES:
        - Los conceptos facturados proceden de facturas_items.
        - Los servicios se seleccionan desde el catálogo de servicios.
        - Los cálculos monetarios deben realizarse y validarse en el backend.
        - No permitir modificar arbitrariamente precios históricos.
        - La gestión corresponde al administrador y al recepcionista.
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/facturacion/facturas.js') }}"></script>
@endpush
