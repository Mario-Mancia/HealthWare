@extends('layouts.app_layout')

@section('titulo', 'Estados')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/estados.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de estados</h1>


    {{--
        VISTA: Administración de estados.

        OBJETIVO:
        Gestionar los estados generales utilizados por las entidades
        que dependen de este catálogo.

        CONTENIDO:
        - Encabezado.
        - Botón para registrar estado.
        - Tabla con identificador, nombre y descripción.
        - Búsqueda por nombre.
        - Modal de creación.
        - Modal de edición.
        - Confirmación de eliminación.

        INTERACCIÓN:
        - Crear, consultar, actualizar y eliminar estados.
        - Mostrar notificaciones de operaciones.

        CONSIDERACIONES:
        - El catálogo se utiliza principalmente para el estado de los usuarios.
        - No confundir estos estados con los estados específicos de citas,
          facturas u otras entidades que utilizan valores propios.
        - Evitar eliminar estados que estén siendo utilizados.
        - La administración corresponde exclusivamente al administrador.
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/estado/estados.js') }}"></script>
@endpush
