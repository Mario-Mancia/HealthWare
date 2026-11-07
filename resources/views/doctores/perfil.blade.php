@extends('layouts.app_layout')

@section('titulo', 'Perfil Doctor')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/doctores.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de perfil de doctores</h1>


    {{--
        VISTA: Perfil profesional del doctor.

        OBJETIVO:
        Presentar la información profesional y laboral de un doctor
        seleccionado.

        CONTENIDO:
        1. ENCABEZADO:
           - Nombre completo.
           - Especialidad.
           - Número de licencia.
           - Estado del doctor.

        2. INFORMACIÓN PROFESIONAL:
           - DUI.
           - Fecha de nacimiento.
           - Sexo.
           - Descripción profesional.
           - Datos de contacto provenientes del usuario relacionado.

        3. HORARIOS:
           - Días laborales.
           - Hora de inicio.
           - Hora de finalización.
           - Horarios registrados.

        4. AGENDA:
           - Próximas citas.
           - Citas recientes.
           - Acceso a la agenda completa.

        5. ACCIONES:
           - Editar información profesional.
           - Consultar horarios.
           - Consultar agenda.

        CONSIDERACIONES:
        - No duplicar la gestión general de doctores.
        - Los horarios deben proceder de la entidad horario_doctors.
        - No exponer datos personales innecesarios del usuario relacionado.

        NOTA:
        - Crear posteriormente la ruta y acción que permitan mostrar
          el perfil individual.
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/doctor/doctores.js') }}"></script>
@endpush
