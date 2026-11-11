@extends('layouts.app_layout')

@section('titulo', 'Expediente Paciente')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/pacientes.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de expediente de pacientes</h1>


    {{--
        VISTA: Expediente clínico integral del paciente.

        OBJETIVO:
        Centralizar la información médica del paciente en una única interfaz.
        Esta será una de las pantallas más importantes de HealthWare.

        ESTRUCTURA:

        1. ENCABEZADO DEL PACIENTE:
           - Nombres y apellidos.
           - Identificador del paciente.
           - Edad y sexo.
           - Tipo sanguíneo.
           - Información de contacto resumida.
           - Acceso al perfil administrativo.

        2. RESUMEN CLÍNICO:
           - Alergias registradas.
           - Condiciones médicas existentes.
           - Diagnósticos relevantes.
           - Última consulta médica.
           - Información clínica importante.

        3. NAVEGACIÓN INTERNA:
           Organizar la información mediante pestañas o secciones:
           - Resumen.
           - Historial de consultas.
           - Diagnósticos.
           - Signos vitales.
           - Condiciones médicas.
           - Alergias.
           - Recetas y tratamientos.

        4. HISTORIAL MÉDICO:
           - Listado cronológico de registros médicos.
           - Fecha de atención.
           - Doctor responsable.
           - Motivo de consulta.
           - Observaciones y evolución.
           - Diagnósticos asociados.

        5. SIGNOS VITALES:
           - Presión arterial.
           - Frecuencia cardíaca.
           - Temperatura.
           - Frecuencia respiratoria.
           - Saturación de oxígeno.
           - Peso y talla, si están contemplados.
           - Fecha y hora de medición.

        6. ALERGIAS Y CONDICIONES:
           - Mostrar las alergias vinculadas al paciente.
           - Mostrar sus condiciones médicas.
           - Permitir agregar o retirar asociaciones según permisos.
           - Indicar observaciones y fechas cuando existan.

        7. RECETAS:
           - Historial de recetas emitidas.
           - Doctor responsable.
           - Fecha de emisión.
           - Medicamentos prescritos.
           - Dosis, frecuencia y duración.
           - Indicaciones adicionales.

        8. ACCIONES:
           - Registrar una nueva consulta médica.
           - Asociar diagnósticos a un registro.
           - Registrar signos vitales.
           - Gestionar alergias y condiciones.
           - Emitir una receta.
           - Consultar registros anteriores.

        CONSIDERACIONES:
        - Esta pantalla integra información de varias entidades.
        - No es necesario crear un controlador único para todas las operaciones.
        - Los controladores especializados pueden proporcionar datos mediante
          consultas independientes o solicitudes AJAX.
        - Los registros deben mostrarse cronológicamente.
        - Restringir estrictamente el acceso a la información clínica.
        - El médico debe trabajar únicamente con pacientes autorizados.
        - No permitir modificar registros clínicos históricos sin las
          reglas de auditoría correspondientes.

        NOTA:
        - Las operaciones de registros médicos, diagnósticos, signos vitales,
          alergias, condiciones y recetas se integrarán aquí.
        - Evitar presentar estas relaciones como módulos independientes
          dentro de la navegación principal.
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/paciente/pacientes.js') }}"></script>
@endpush
