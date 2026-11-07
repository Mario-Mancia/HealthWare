@extends('layouts.app_layout')

@section('titulo', 'Alergias')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/alergias.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de alergias</h1>


    {{--
        VISTA: Catálogo de alergias.

        OBJETIVO:
        Mantener el listado de alergias que pueden registrarse
        en los expedientes clínicos de los pacientes.

        CONTENIDO:
        - Encabezado.
        - Botón para agregar alergia.
        - Tabla con identificador, nombre y descripción.
        - Búsqueda por nombre.
        - Modal de creación.
        - Modal de edición.
        - Confirmación de eliminación.

        FORMULARIO:
        - Nombre de la alergia.
        - Descripción opcional.

        INTERACCIÓN:
        - Crear, consultar, actualizar y eliminar registros.
        - Presentar errores de validación.

        CONSIDERACIONES:
        - La alergia del catálogo se vincula a pacientes mediante pacientes_alergias.
        - La asociación de una alergia con un paciente se administra
          desde su expediente.
        - La consulta está permitida para administrador y médico.
        - La administración del catálogo corresponde al administrador.
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/alergia/alergias.js') }}"></script>
@endpush
