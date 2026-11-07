@extends('layouts.app_layout')

@section('titulo', 'Roles')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/roles.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de roles</h1>


    {{--
        VISTA: Administración de roles del sistema.

        OBJETIVO:
        Gestionar los roles que determinan los niveles de acceso
        de los usuarios de HealthWare.

        CONTENIDO:
        - Encabezado.
        - Botón para registrar un rol.
        - Tabla con identificador, nombre del rol y descripción.
        - Búsqueda por nombre.
        - Modal de creación.
        - Modal de edición.
        - Confirmación de eliminación.

        FORMULARIO:
        - Nombre del rol.
        - Descripción.

        INTERACCIÓN:
        - Crear, consultar, actualizar y eliminar roles.
        - Presentar notificaciones de éxito o error.

        CONSIDERACIONES:
        - Los roles actuales son Administrador, Medico y Recepcionista.
        - Evitar eliminar roles asignados a usuarios.
        - No permitir operaciones que dejen usuarios sin un rol válido.
        - La administración es exclusiva del administrador.
        - Los permisos efectivos se controlan mediante middleware y
          las políticas de autorización correspondientes.
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/rol/roles.js') }}"></script>
@endpush
