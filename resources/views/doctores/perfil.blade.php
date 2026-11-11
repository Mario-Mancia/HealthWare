@extends('layouts.app_layout')

@section('titulo', 'Perfil Doctor')

@section('contenido')

    <div class="hw-page">

        <header class="hw-header">
            <div class="hw-heading">
                <div class="hw-heading-icon">
                    <span class="material-symbols-outlined">badge</span>
                </div>
                <div>
                    <h1 class="hw-title">Perfil profesional</h1>
                    <p class="hw-subtitle">Datos profesionales, horarios y agenda del doctor.</p>
                </div>
            </div>
        </header>

        <section class="hw-surface">
            <div class="hw-placeholder">
                <span class="material-symbols-outlined">construction</span>
                <h2>Pantalla pendiente de conectar</h2>
                <p>
                    El diseño base ya usa el layout y los componentes de HealthWare.
                    Falta definir la ruta y la acción del controlador que cargan sus datos.
                </p>
            </div>
        </section>

    </div>

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
