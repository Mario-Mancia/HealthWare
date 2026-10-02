<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        const tema = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        document.documentElement.setAttribute('data-bs-theme', tema);
    </script>

    <title>@yield('titulo', 'HealthWare')</title>

    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global-styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app-layout.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    @stack('estilos')
</head>
<body class="d-flex flex-column">

{{-- TOPBAR BRAND --}}
<header class="app-topbar d-flex align-items-center px-3 px-lg-4">
    {{-- Botón menú hamburguesa (Solo visible en móviles) --}}
    <button class="btn btn-link text-inherit d-lg-none me-2 p-0" style="color: inherit;" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas">
        <span class="material-symbols-outlined fs-2">menu</span>
    </button>

    {{-- Branding --}}
    <a class="navbar-brand d-flex align-items-center fw-bold gap-2" style="color: inherit;" href="#">
        <span class="material-symbols-outlined fs-2">health_and_safety</span>
        <span class="fs-4">HealthWare</span>
    </a>

    {{-- Espaciador --}}
    <div class="ms-auto"></div>

    {{-- Menú de Usuario Dropdown --}}
    <div class="dropdown">
        <button class="btn btn-link text-decoration-none d-flex align-items-center gap-2 p-0" style="color: inherit;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="user-avatar rounded-circle d-flex align-items-center justify-content-center shadow-sm">
                <span class="material-symbols-outlined">person</span>
            </div>
            <span class="d-none d-md-block fw-medium">
                    {{ Auth::user()->nombres ?? 'Administrador' }}
                </span>
            <span class="material-symbols-outlined fs-5 d-none d-md-block opacity-75">expand_more</span>
        </button>

        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2 rounded-3" style="min-width: 220px;">
            <li>
                <a class="dropdown-item rounded-2 d-flex align-items-center gap-2 py-2 mb-1 fw-medium" href="#">
                    <span class="material-symbols-outlined fs-5 opacity-75">manage_accounts</span>
                    Perfil
                </a>
            </li>
            <li><hr class="dropdown-divider opacity-10"></li>
            <li>
                {{-- Formulario Cerrar Sesión --}}
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="dropdown-item btn-soft-danger">
                        <span class="material-symbols-outlined fs-5">logout</span>
                        Cerrar Sesión
                    </button>
                </form>
            </li>
        </ul>
    </div>
</header>

{{-- CONTENEDOR PRINCIPAL --}}
<div class="d-flex flex-grow-1 overflow-hidden">

    {{-- SIDEBAR LATERAL --}}
    <aside class="offcanvas-lg offcanvas-start app-sidebar px-3 pb-3" tabindex="-1" id="sidebarOffcanvas">
        <div class="offcanvas-header d-lg-none">
            <h5 class="offcanvas-title fw-bold d-flex align-items-center gap-2" style="color: var(--md-sys-color-on-brand-bg);">
                <span class="material-symbols-outlined">health_and_safety</span>
                HealthWare
            </h5>
            <button type="button" class="btn-close" style="filter: invert(1) grayscale(100%) brightness(200%);" data-bs-dismiss="offcanvas" data-bs-target="#sidebarOffcanvas"></button>
        </div>

        <div class="offcanvas-body flex-column p-0 pt-lg-2 h-100">
            <h6 class="text-uppercase fw-bold px-3 mb-2 mt-2" style="font-size: 0.7rem; letter-spacing: 1px; opacity: 0.6;">
                Menú
            </h6>

            <nav class="d-flex flex-column flex-grow-1 gap-1">
                {{-- Rutas Dinámicas: Se activa solo si coincide con el nombre de la ruta --}}
                <a href="{{ route('home') }}" class="sidebar-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    <span class="material-symbols-outlined me-2">dashboard</span>
                    Dashboard
                </a>

                <a href="{{ route('usuarios') }}" class="sidebar-link {{ request()->routeIs('usuarios*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined me-2">group</span>
                    Gestión de usuarios
                </a>

                {{-- Puedes ir agregando más enlaces de esta misma forma --}}
            </nav>

            {{-- Footer del Sidebar --}}
            <div class="mt-auto px-3 text-center" style="font-size: 0.75rem; opacity: 0.6;">
                &copy; {{ date('Y') }} HealthWare.<br>Versión 1.0.0
            </div>
        </div>
    </aside>

    {{-- PANTALLA INTERNA FLOTANTE  --}}
    <main class="app-screen d-flex flex-column flex-grow-1 position-relative p-4">
        @yield('contenido')
    </main>

</div>

@include('components.toast')

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/toast.js') }}"></script>
@stack('scripts')
</body>
</html>
