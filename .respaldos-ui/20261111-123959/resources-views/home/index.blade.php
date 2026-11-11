{{-- Vista generada por reestructurar-vistas.sh: home.index --}}
@extends('layouts.app_layout')

@section('titulo', 'Home')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('contenido')
    <h1 class="fw-bold fs-1">Ruta: home.index</h1>



    {{--
        VISTA: Página principal de HealthWare.

        OBJETIVO:
        Presentar una pantalla de bienvenida al usuario autenticado y servir
        como punto de entrada al sistema.

        CONTENIDO:
        - Encabezado de bienvenida con el nombre del usuario autenticado.
        - Breve descripción del propósito del sistema.
        - Accesos rápidos a los módulos disponibles según el rol.
        - Resumen de las principales funciones de HealthWare.
        - Indicadores generales opcionales, sin duplicar el Dashboard.

        INTERACCIÓN:
        - Botones o tarjetas que redirijan a los módulos correspondientes.
        - Mostrar únicamente accesos permitidos para el rol del usuario.

        CONSIDERACIONES:
        - No convertir esta pantalla en otro Dashboard.
        - Mantener un diseño limpio, sencillo y orientado a la navegación.
        - Utilizar el layout principal app_layout.
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/home/recetas.js') }}"></script>
@endpush
