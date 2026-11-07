@extends('layouts.app_layout')

@section('titulo', 'Recetas')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/recetas.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de recetas médicas</h1>


    {{--
        VISTA: Consulta y gestión general de recetas médicas.

        OBJETIVO:
        Proporcionar una vista para consultar recetas registradas y
        acceder a sus respectivos detalles.

        CONTENIDO:
        1. ENCABEZADO:
           - Título "Recetas médicas".
           - Botón para emitir una receta, según permisos.

        2. FILTROS:
           - Paciente.
           - Doctor.
           - Fecha de emisión.
           - Estado de la receta, si está contemplado.

        3. TABLA:
           - Identificador de receta.
           - Paciente.
           - Doctor responsable.
           - Fecha de emisión.
           - Cantidad de medicamentos.
           - Acciones.

        4. FORMULARIO DE RECETA:
           - Seleccionar paciente.
           - Asociar registro médico, cuando corresponda.
           - Seleccionar doctor responsable.
           - Fecha de emisión.
           - Indicaciones generales.

        5. DETALLE DE MEDICAMENTOS:
           - Medicamento.
           - Dosis.
           - Frecuencia.
           - Duración.
           - Cantidad prescrita.
           - Indicaciones particulares.

        6. ACCIONES:
           - Consultar receta.
           - Agregar medicamentos.
           - Consultar expediente del paciente.
           - Preparar una versión imprimible de la receta.

        CONSIDERACIONES:
        - Las recetas_items representan los medicamentos de cada receta.
        - La creación de la receta y sus detalles debe mantener integridad
          mediante una transacción cuando corresponda.
        - El catálogo de medicamentos debe proporcionar los productos
          seleccionables.
        - El médico es responsable de las operaciones clínicas.
        - Esta vista puede funcionar como listado general, mientras que
          el expediente del paciente muestra su historial individual.
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/recetas/recetas.js') }}"></script>
@endpush
