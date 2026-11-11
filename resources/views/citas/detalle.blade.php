@extends('layouts.app_layout')

@section('titulo', 'Detalle Cita')

@section('contenido')

    <div class="hw-page">

        <header class="hw-header">
            <div class="hw-heading">
                <div class="hw-heading-icon">
                    <span class="material-symbols-outlined">event_note</span>
                </div>
                <div>
                    <h1 class="hw-title">Detalle de la cita</h1>
                    <p class="hw-subtitle">Información contextual de la cita y acciones según su estado.</p>
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
        VISTA: Detalle individual de una cita médica.

        OBJETIVO:
        Mostrar toda la información de una cita seleccionada y permitir
        realizar las acciones correspondientes según su estado.

        CONTENIDO:
        1. INFORMACIÓN GENERAL:
           - Identificador de la cita.
           - Fecha y hora.
           - Estado actual.
           - Motivo de consulta.

        2. PACIENTE:
           - Nombre completo.
           - Identificador.
           - Acceso a su perfil y expediente, según permisos.

        3. DOCTOR:
           - Nombre completo.
           - Especialidad.
           - Horario relacionado.

        4. ACCIONES:
           - Editar cita.
           - Confirmar o cambiar estado.
           - Cancelar cita.
           - Consultar expediente del paciente.
           - Iniciar o consultar el registro médico relacionado,
             cuando exista.

        CONSIDERACIONES:
        - No duplicar la pantalla general de citas.
        - Mostrar información contextual de la cita.
        - Restringir las operaciones según el rol.
        - Validar las transiciones de estado.

        NOTA:
        - Definir la ruta y acción del controlador necesarias para
          cargar esta vista.
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/cita/citas.js') }}"></script>
@endpush
