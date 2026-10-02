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
<body class="d-flex flex-column min-vh-100">

        {{-- TOPBAR PRINCIPAL --}}
        <nav class="navbar navbar-expand-lg navbar-healthware shadow-sm py-2">
            <div class="container-fluid px-4">
                <a class="navbar-brand d-flex align-items-center fw-bold gap-2" href="#">
                    <span class="material-symbols-outlined fs-2">health_and_safety</span>
                    HealthWare
                </a>
            </div>
        </nav>

        {{-- CONTENIDO PRINCIPAL --}}
        <main class="container-fluid px-3 px-md-4 py-4 flex-grow-1 d-flex flex-column align-items-center justify-content-center">
            @yield('contenido')
        </main>

        {{-- FOOTER PRINCIPAL --}}
        <footer class="py-3 mt-auto footer-healthware">
            <div class="container text-center">
                    <span class="fw-medium footer-text">
                        &copy; {{ date('Y') }} HealthWare. Sistema de Gestión Clínica.
                    </span>
            </div>
        </footer>

    {{-- Componentes reutilizables --}}
@include('components.toast')

    {{-- Sección de scripts --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/toast.js') }}"></script>
@stack('scripts')
</body>
</html>
