@extends('layouts.app_layout')

@section('titulo', 'Facturas')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/citas.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de citas</h1>


    {{--
        VISTA: Gestión y programación de citas médicas.

        OBJETIVO:
        Permitir consultar, programar y administrar las citas de la clínica.

        CONTENIDO:
        1. ENCABEZADO:
           - Título "Agenda de citas".
           - Botón para programar una cita.

        2. FILTROS:
           - Fecha o rango de fechas.
           - Paciente.
           - Doctor.
           - Estado de la cita.
           - Tipo de vista: calendario o listado.

        3. CALENDARIO:
           - Vista mensual, semanal o diaria.
           - Mostrar citas según fecha y hora.
           - Identificar citas mediante colores según su estado.
           - Diferenciar horarios disponibles y ocupados.

        4. TABLA ALTERNATIVA:
           - Fecha.
           - Hora.
           - Paciente.
           - Doctor.
           - Motivo de consulta.
           - Estado.
           - Acciones.

        5. MODALES:
           - Programar cita.
           - Editar cita.
           - Consultar detalles.
           - Confirmar cancelación o eliminación según la lógica del sistema.

        6. FORMULARIO DE CITA:
           - Seleccionar paciente.
           - Seleccionar doctor.
           - Seleccionar fecha.
           - Seleccionar horario disponible.
           - Motivo de consulta.
           - Observaciones, si corresponde.

        INTERACCIÓN:
        - Consultar disponibilidad de horarios.
        - Evitar conflictos de agenda.
        - Actualizar el calendario después de cada operación.
        - Acceder al detalle de una cita.

        CONSIDERACIONES:
        - El estado de una cita debe respetar los valores definidos en el modelo.
        - Validar que el doctor tenga disponibilidad.
        - Validar que no existan citas incompatibles en el mismo horario.
        - La recepción administra las citas; el médico consulta su agenda
          según los permisos establecidos.
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/cita/citas.js') }}"></script>
@endpush
