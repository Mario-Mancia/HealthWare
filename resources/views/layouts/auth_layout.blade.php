
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('titulo', 'HealthWare')</title>

    {{-- Inicialización temprana del tema --}}
    <script src="{{ asset('js/global-scripts.js') }}"></script>

    {{-- Bootstrap --}}
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">

    {{-- Material Design System --}}
    <link rel="stylesheet" href="{{ asset('css/global-styles.css') }}">

    {{-- Material Symbols --}}
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">

    {{-- Estilos particulares de cada vista --}}
    @stack('estilos')
</head>

<body class="d-flex flex-column min-vh-100">

{{-- APP BAR --}}
<nav class="navbar navbar-healthware py-2">
    <div class="container-fluid px-4">

        <a class="navbar-brand d-flex align-items-center gap-2"
           href="{{ url('/') }}">

                <span class="material-symbols-outlined fs-2">
                    health_and_safety
                </span>

            <span>HealthWare</span>
        </a>

        <button
            type="button"
            class="btn btn-icon"
            id="themeToggleBtn"
            title="Cambiar tema"
            aria-label="Cambiar tema"
            aria-pressed="false">

                <span class="material-symbols-outlined" id="themeIcon">
                    dark_mode
                </span>
        </button>

    </div>
</nav>

{{-- MAIN CONTENT --}}
<main class="container-fluid px-3 px-md-4 py-4 flex-grow-1
                 d-flex flex-column align-items-center justify-content-center">

    @yield('contenido')

</main>

{{-- FOOTER --}}
<footer class="footer-healthware mt-auto">
    <div class="container text-center">
            <span class="footer-text">
                &copy; {{ date('Y') }} HealthWare.
                Sistema de Gestión Clínica.
            </span>
    </div>
</footer>

{{-- REUSABLE COMPONENTS --}}
@include('components.toast')

{{-- FRAMEWORKS --}}
<script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>

{{-- GLOBAL SCRIPTS --}}
<script src="{{ asset('js/toast.js') }}"></script>

{{-- PARTICULAR SCRIPTS --}}
@stack('scripts')

</body>
</html>
