@extends('layouts.app_layout')

@section('titulo', 'Usuarios')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/usuarios.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de usuarios</h1>

    {{--
        -Filtros de búsqueda de texto, roles, estados
        -Tabla de usuarios
        - modales de registrar, editar y eliminar
        - Formularios en cada uno.

    --}}
@endsection

@push('scripts')
    <script src="{{ asset('js/usuario/usuarios.js') }}"></script>
@endpush
