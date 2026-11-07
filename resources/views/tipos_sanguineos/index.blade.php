@extends('layouts.app_layout')

@section('titulo', 'Tipos Sanguineos')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/tipos_sanguineos.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de tipos sanguineos</h1>


    {{--
        VISTA: Catálogo de tipos sanguíneos.

        OBJETIVO:
        Consultar los tipos sanguíneos disponibles para asignarlos
        a los pacientes.

        CONTENIDO:
        - Encabezado y descripción del catálogo.
        - Botón para agregar un tipo sanguíneo, si el usuario tiene permisos.
        - Tabla con identificador y denominación del tipo sanguíneo.
        - Búsqueda por denominación.
        - Modal de creación.
        - Modal de edición.
        - Confirmación de eliminación.

        INTERACCIÓN:
        - Crear, consultar, actualizar y eliminar registros.
        - Mostrar errores de validación.
        - Actualizar la tabla después de cada operación.

        CONSIDERACIONES:
        - El catálogo es utilizado por el formulario de pacientes.
        - No eliminar registros que estén siendo utilizados por pacientes.
        - La consulta está permitida para los tres roles.
        - La administración corresponde al administrador.
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/tipo_sanguineo/tipos_sanguineos.js') }}"></script>
@endpush
