<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">

    {{-- Asignación dinámica de tema del sistema --}}
    <script>
        const tema = window.matchMedia(
            '(prefers-color-scheme: dark)'
        ).matches ? 'dark' : 'light';

        document.documentElement.setAttribute(
            'data-bs-theme',
            tema
        );
    </script>

    <title>@yield('titulo', 'HealthWare')</title>

    {{-- SECCIÓN DE ESTILOS --}}
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{asset('css/global-styles.css')}}">
    {{-- Iconos de Material --}}
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    {{-- Carga de estilos particulares --}}
    @stack('estilos')
</head>
<body>




    {{-- SECCIÓN DE COMPONENTES --}}
    @include('components.toast')

    {{-- SECCIÓN DE SCRIPTS --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{asset('bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('js/toast.js')}}"></script>
    @stack('scripts')
</body>
</html>
