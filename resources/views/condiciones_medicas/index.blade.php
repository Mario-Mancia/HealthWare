@extends('layouts.app_layout')

@section('titulo', 'Condiciones Médicas')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/condiciones_medicas.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de condiciones medicas</h1>


    {{--
        VISTA: Catálogo de condiciones médicas.

        OBJETIVO:
        Administrar las condiciones médicas que pueden asociarse
        a los pacientes.

        CONTENIDO:
        - Encabezado.
        - Botón para agregar condición médica.
        - Tabla con identificador, nombre y descripción.
        - Búsqueda por denominación.
        - Modal de creación.
        - Modal de edición.
        - Confirmación de eliminación.

        INTERACCIÓN:
        - Crear, consultar, actualizar y eliminar condiciones.
        - Mostrar mensajes de validación.

        CONSIDERACIONES:
        - Este catálogo se relaciona con pacientes mediante condiciones_pacientes.
        - No confundir una condición del catálogo con la condición
          que tiene registrada un paciente.
        - La asociación individual se administra desde el expediente clínico.
        - Consulta para administrador y médico.
        - Administración del catálogo exclusiva del administrador.
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/condicion_medica/condiciones_medicas.js') }}"></script>
@endpush
