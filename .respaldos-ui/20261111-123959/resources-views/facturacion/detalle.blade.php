@extends('layouts.app_layout')

@section('titulo', 'Detalle Factura')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/facturacion.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de detalle de facturas</h1>


    {{--
        VISTA: Detalle individual de una factura.

        OBJETIVO:
        Mostrar la información completa de una factura y sus conceptos
        facturados.

        CONTENIDO:
        1. ENCABEZADO:
           - Número de factura.
           - Fecha de emisión.
           - Estado de la factura.

        2. DATOS DE LA CLÍNICA:
           - Nombre comercial.
           - Información institucional disponible.

        3. DATOS DEL PACIENTE:
           - Nombre completo.
           - Identificador.
           - Información de facturación disponible.

        4. DETALLE DE CONCEPTOS:
           - Servicio.
           - Descripción.
           - Cantidad.
           - Precio unitario.
           - Subtotal.

        5. RESUMEN:
           - Subtotal general.
           - Descuentos o impuestos, si corresponden.
           - Total final.
           - Saldo pendiente, cuando se implemente el registro de pagos.

        6. PAGOS:
           - Historial de pagos relacionados.
           - Fecha.
           - Monto.
           - Metodo de pago.
           - Estado de la transacción.

        7. ACCIONES:
           - Imprimir factura.
           - Generar representación PDF, si se implementa.
           - Registrar pago, cuando esté disponible.
           - Regresar al listado.

        CONSIDERACIONES:
        - Los conceptos se obtienen de facturas_items.
        - Los pagos pertenecen a la entidad pagos y se integrarán cuando
          sus operaciones estén implementadas.
        - No inventar información de transacciones inexistentes.
        - La factura debe conservar su historial y trazabilidad.
        - El detalle debe ser adecuado para impresión.
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/facturacion/facturas.js') }}"></script>
@endpush
