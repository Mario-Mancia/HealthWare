@extends('layouts.app_layout')

@section('titulo', 'Diagnósticos')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/diagnosticos.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de diagnosticos</h1>


    {{--
        VISTA: Catálogo de diagnósticos médicos.

        OBJETIVO:
        Mantener un catálogo de diagnósticos que puedan asociarse
        a los registros médicos de los pacientes.

        CONTENIDO:
        - Encabezado y descripción.
        - Botón para registrar diagnóstico.
        - Tabla de diagnósticos.
        - Identificador.
        - Nombre o denominación.
        - Descripción.
        - Búsqueda por nombre.
        - Modal de creación.
        - Modal de edición.
        - Confirmación de eliminación.

        INTERACCIÓN:
        - Consultar diagnósticos.
        - Administrar el catálogo según permisos.
        - Mostrar errores y confirmaciones.

        CONSIDERACIONES:
        - Los diagnósticos se asocian a registros médicos mediante
          registro_medico_diagnosticos.
        - No confundir el catálogo con los diagnósticos particulares
          de un paciente.
        - La consulta está permitida para administrador y médico.
        - La modificación del catálogo corresponde al administrador.
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/diagnostico/diagnosticos.js') }}"></script>
@endpush
