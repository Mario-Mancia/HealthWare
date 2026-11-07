@extends('layouts.app_layout')

@section('titulo', 'Registros Médicos')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/registros_medicos.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de Registros médicos</h1>


    {{--
        VISTA: Administración y consulta de registros médicos.

        OBJETIVO:
        Proporcionar una interfaz para consultar los registros clínicos
        y acceder al expediente correspondiente.

        CONTENIDO:
        1. ENCABEZADO:
           - Título "Registros médicos".
           - Descripción del módulo.

        2. FILTROS:
           - Paciente.
           - Doctor responsable.
           - Fecha o rango de fechas.
           - Diagnóstico asociado, si resulta útil.

        3. TABLA:
           - Identificador del registro.
           - Paciente.
           - Doctor responsable.
           - Fecha de atención.
           - Motivo de consulta.
           - Resumen u observación.
           - Acciones.

        4. FORMULARIO / MODAL:
           - Seleccionar paciente.
           - Seleccionar doctor, cuando corresponda.
           - Asociar cita, si existe.
           - Registrar motivo de consulta.
           - Registrar observaciones y evolución.
           - Guardar el registro.

        5. ACCIONES:
           - Consultar registro.
           - Acceder al expediente clínico.
           - Consultar diagnósticos asociados.
           - Eliminar o anular según las reglas del sistema.

        CONSIDERACIONES:
        - Los diagnósticos asociados se administran mediante la relación
          registro_medico_diagnosticos.
        - No duplicar la gestión de diagnósticos en esta pantalla.
        - El expediente del paciente será la interfaz principal para
          consultar su historial clínico.
        - Proteger los datos clínicos y mantener trazabilidad de operaciones.
        - No asumir que todos los usuarios pueden crear o eliminar registros.
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/registro_medico/registroMedico.js') }}"></script>
@endpush
