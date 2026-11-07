@extends('layouts.app_layout')

@section('titulo', 'Horarios Doctores')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/horarios.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de horarios</h1>


    {{--
        VISTA: Administración de horarios médicos.

        OBJETIVO:
        Gestionar la disponibilidad laboral de los doctores.

        CONTENIDO:
        1. ENCABEZADO:
           - Título "Horarios médicos".
           - Botón para registrar un horario.

        2. FILTROS:
           - Doctor.
           - Día de la semana.
           - Especialidad, si resulta útil.

        3. VISTA DE HORARIOS:
           - Vista semanal organizada por días.
           - Identificación del doctor.
           - Hora de inicio y finalización.
           - Representación visual de los intervalos.

        4. TABLA:
           - Doctor.
           - Día de la semana.
           - Hora de inicio.
           - Hora de finalización.
           - Acciones.

        5. MODALES:
           - Registrar horario.
           - Editar horario.
           - Confirmar eliminación.

        6. FORMULARIO:
           - Seleccionar doctor.
           - Seleccionar día de la semana.
           - Hora de inicio.
           - Hora de finalización.

        INTERACCIÓN:
        - Consultar horarios de un doctor.
        - Crear, editar y eliminar horarios.
        - Mostrar los horarios agrupados por doctor o día.

        CONSIDERACIONES:
        - Validar que la hora de finalización sea posterior a la inicial.
        - Evitar intervalos superpuestos para un mismo doctor.
        - Respetar el esquema de días de semana definido en la base de datos.
        - Los horarios sirven como referencia para la disponibilidad
          al programar citas.
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/horarios/horarios.js') }}"></script>
@endpush
