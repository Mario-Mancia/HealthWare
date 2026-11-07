
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('titulo', 'HealthWare')</title>

    {{-- Inicialización global del tema --}}
    <script src="{{ asset('js/global-scripts.js') }}"></script>

    {{-- Framework y Design System --}}
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global-styles.css') }}">

    {{-- Estilos del layout --}}
    <link rel="stylesheet" href="{{ asset('css/sidebar-modulos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app-layout.css') }}">

    {{-- Material Symbols --}}
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">

    {{-- Estilos particulares de cada vista --}}
    @stack('estilos')
</head>

<body class="app-body">

@php
    /*
    |--------------------------------------------------------------------------
    | Usuario autenticado
    |--------------------------------------------------------------------------
    */

    $usuario = Auth::user();

    $nombreUsuario = trim(
        ($usuario->nombres ?? '') . ' ' .
        ($usuario->apellidos ?? '')
    );

    $nombreUsuario = $nombreUsuario ?: 'Usuario';

    $iniciales = mb_strtoupper(
        mb_substr($usuario->nombres ?? 'U', 0, 1) .
        mb_substr($usuario->apellidos ?? '', 0, 1)
    );

    $rolUsuario = mb_strtolower(
        trim($usuario->rol->rol ?? '')
    );

    /*
    |--------------------------------------------------------------------------
    | Permisos de navegación
    |--------------------------------------------------------------------------
    */

    $rolesTodos = [
        'administrador',
        'médico',
        'recepcionista',
    ];

    $rolesAdminMedico = [
        'administrador',
        'médico',
    ];

    $rolesAdminRecepcionista = [
        'administrador',
        'recepcionista',
    ];

    $soloAdmin = [
        'administrador',
    ];

    /*
    |--------------------------------------------------------------------------
    | Configuración de módulos
    |--------------------------------------------------------------------------
    */

    $menuModulos = [

        [
            'id' => 'pacientes',
            'titulo' => 'Pacientes',
            'icono' => 'personal_injury',

            'enlaces' => [
                [
                    'ruta' => 'pacientes',
                    'etiqueta' => 'Gestión de pacientes',
                    'icono' => 'groups',
                    'roles' => $rolesTodos,
                ],
            ],
        ],

        [
            'id' => 'atencion',
            'titulo' => 'Atención médica',
            'icono' => 'stethoscope',

            'enlaces' => [
                [
                    'ruta' => 'citas',
                    'etiqueta' => 'Citas médicas',
                    'icono' => 'calendar_month',
                    'roles' => $rolesTodos,
                ],
                [
                    'ruta' => 'registros_medicos',
                    'etiqueta' => 'Expedientes clínicos',
                    'icono' => 'clinical_notes',
                    'roles' => $rolesAdminMedico,

                    'rutas_activas' => [
                        'registro_medico_diagnosticos',
                        'pacientes_condiciones',
                        'pacientes_alergias',
                        'recetas',
                        'detalle_recetas',
                    ],
                ],
            ],
        ],

        [
            'id' => 'personal',
            'titulo' => 'Personal médico',
            'icono' => 'badge',

            'enlaces' => [
                [
                    'ruta' => 'doctores',
                    'etiqueta' => 'Doctores',
                    'icono' => 'medical_services',
                    'roles' => $rolesTodos,
                ],
                [
                    'ruta' => 'horarios',
                    'etiqueta' => 'Horarios de doctores',
                    'icono' => 'schedule',
                    'roles' => $rolesTodos,
                ],
                [
                    'ruta' => 'especialidades',
                    'etiqueta' => 'Especialidades',
                    'icono' => 'category',
                    'roles' => $rolesTodos,
                ],
            ],
        ],

        [
            'id' => 'facturacion',
            'titulo' => 'Facturación',
            'icono' => 'payments',

            'enlaces' => [
                [
                    'ruta' => 'facturas',
                    'etiqueta' => 'Facturación',
                    'icono' => 'receipt_long',
                    'roles' => $rolesAdminRecepcionista,

                    'rutas_activas' => [
                        'detalle_facturas',
                    ],
                ],
                [
                    'ruta' => 'servicios',
                    'etiqueta' => 'Servicios',
                    'icono' => 'sell',
                    'roles' => $rolesTodos,
                ],
            ],
        ],

        [
            'id' => 'comunicacion',
            'titulo' => 'Comunicación',
            'icono' => 'forum',

            'enlaces' => [
                [
                    'ruta' => 'anuncios_globales',
                    'etiqueta' => 'Anuncios globales',
                    'icono' => 'campaign',
                    'roles' => $rolesTodos,
                ],
                [
                    'ruta' => 'notificaciones',
                    'etiqueta' => 'Notificaciones',
                    'icono' => 'notifications',
                    'roles' => $rolesTodos,
                ],
            ],
        ],

        [
            'id' => 'catalogos',
            'titulo' => 'Catálogos clínicos',
            'icono' => 'folder_open',

            'enlaces' => [
                [
                    'ruta' => 'tipos-sanguineos',
                    'etiqueta' => 'Tipos sanguíneos',
                    'icono' => 'bloodtype',
                    'roles' => $rolesTodos,
                ],
                [
                    'ruta' => 'diagnosticos',
                    'etiqueta' => 'Diagnósticos',
                    'icono' => 'diagnosis',
                    'roles' => $rolesAdminMedico,
                ],
                [
                    'ruta' => 'condiciones-medicas',
                    'etiqueta' => 'Condiciones médicas',
                    'icono' => 'medical_information',
                    'roles' => $rolesAdminMedico,
                ],
                [
                    'ruta' => 'alergias',
                    'etiqueta' => 'Alergias',
                    'icono' => 'warning',
                    'roles' => $rolesAdminMedico,
                ],
                [
                    'ruta' => 'medicamentos',
                    'etiqueta' => 'Medicamentos',
                    'icono' => 'medication',
                    'roles' => $rolesAdminMedico,
                ],
            ],
        ],

        [
            'id' => 'administracion',
            'titulo' => 'Administración',
            'icono' => 'admin_panel_settings',

            'enlaces' => [
                [
                    'ruta' => 'usuarios',
                    'etiqueta' => 'Gestión de usuarios',
                    'icono' => 'group',
                    'roles' => $soloAdmin,
                ],
                [
                    'ruta' => 'roles',
                    'etiqueta' => 'Roles',
                    'icono' => 'verified_user',
                    'roles' => $soloAdmin,
                ],
                [
                    'ruta' => 'estados',
                    'etiqueta' => 'Estados',
                    'icono' => 'toggle_on',
                    'roles' => $soloAdmin,
                ],
                [
                    'ruta' => 'bitacora',
                    'etiqueta' => 'Bitácora del sistema',
                    'icono' => 'history',
                    'roles' => $soloAdmin,
                ],
            ],
        ],
    ];
@endphp

{{-- =====================================================
     TOPBAR
====================================================== --}}

<header class="app-topbar">

    <div class="app-topbar-start">

        {{-- Mobile navigation --}}
        <button
            type="button"
            class="btn btn-icon app-menu-toggle d-lg-none"
            data-bs-toggle="offcanvas"
            data-bs-target="#sidebarOffcanvas"
            aria-controls="sidebarOffcanvas"
            aria-label="Abrir menú de navegación">

                <span class="material-symbols-outlined">
                    menu
                </span>
        </button>

        {{-- Brand --}}
        <a href="{{ route('home') }}" class="app-brand">

                <span class="material-symbols-outlined app-brand-icon">
                    health_and_safety
                </span>

            <span class="app-brand-name">
                    HealthWare
                </span>

        </a>

    </div>

    {{-- Topbar actions --}}
    <div class="app-topbar-actions">

        {{-- Notifications --}}
        @if(\Illuminate\Support\Facades\Route::has('notificaciones'))

            <a
                href="{{ route('notificaciones') }}"
                class="btn btn-icon app-notification-button"
                aria-label="Ver notificaciones"
                title="Notificaciones">

                    <span class="material-symbols-outlined">
                        notifications
                    </span>

            </a>

        @endif

        {{-- Theme toggle --}}
        <button
            type="button"
            class="btn btn-icon app-theme-button"
            id="themeToggleBtn"
            aria-label="Cambiar tema"
            aria-pressed="false"
            title="Cambiar tema">

                <span class="material-symbols-outlined" id="themeIcon">
                    dark_mode
                </span>

        </button>

        {{-- User dropdown --}}
        <div class="dropdown app-user-dropdown">

            <button
                type="button"
                class="app-user-trigger"
                data-bs-toggle="dropdown"
                data-bs-auto-close="true"
                aria-expanded="false"
                aria-label="Abrir menú de usuario">

                    <span class="app-user-avatar">
                        {{ $iniciales ?: 'U' }}
                    </span>

                <span class="app-user-summary d-none d-sm-flex">

                        <span class="app-user-name">
                            {{ $nombreUsuario }}
                        </span>

                        <span class="app-user-role">
                            {{ $usuario->rol->rol ?? 'Usuario' }}
                        </span>

                    </span>

                <span class="material-symbols-outlined app-user-chevron">
                        expand_more
                    </span>

            </button>

            {{-- User menu --}}
            <ul class="dropdown-menu dropdown-menu-end app-user-menu">

                <li>
                    <div class="app-user-menu-header">

                            <span class="app-user-avatar app-user-avatar-large">
                                {{ $iniciales ?: 'U' }}
                            </span>

                        <div class="app-user-menu-identity">

                                <span class="app-user-menu-name">
                                    {{ $nombreUsuario }}
                                </span>

                            <span class="app-user-menu-email">
                                    {{ $usuario->correo ?? '' }}
                                </span>

                        </div>

                    </div>
                </li>

                <li>
                    <div class="app-user-role-badge">

                            <span class="material-symbols-outlined">
                                verified_user
                            </span>

                        {{ $usuario->rol->rol ?? 'Usuario' }}

                    </div>
                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>

                    <form
                        action="{{ route('logout') }}"
                        method="POST"
                        class="m-0">

                        @csrf

                        <button
                            type="submit"
                            class="dropdown-item app-logout-button">

                                <span class="material-symbols-outlined">
                                    logout
                                </span>

                            Cerrar sesión

                        </button>

                    </form>

                </li>

            </ul>

        </div>

    </div>

</header>


{{-- =====================================================
     WORKSPACE
====================================================== --}}

<div class="app-workspace">

    {{-- SIDEBAR --}}
    <aside
        class="offcanvas-lg offcanvas-start app-sidebar"
        tabindex="-1"
        id="sidebarOffcanvas"
        aria-label="Navegación principal">

        {{-- Mobile sidebar header --}}
        <div class="offcanvas-header app-sidebar-mobile-header d-lg-none">

            <a href="{{ route('home') }}" class="app-brand">

                    <span class="material-symbols-outlined app-brand-icon">
                        health_and_safety
                    </span>

                <span class="app-brand-name">
                        HealthWare
                    </span>

            </a>

            <button
                type="button"
                class="btn btn-icon app-sidebar-close"
                data-bs-dismiss="offcanvas"
                data-bs-target="#sidebarOffcanvas"
                aria-label="Cerrar menú">

                    <span class="material-symbols-outlined">
                        close
                    </span>

            </button>

        </div>

        <div class="offcanvas-body app-sidebar-body">

            {{-- Sidebar heading --}}
            <div class="sidebar-heading">
                    <span class="sidebar-section-label">
                        MENÚ PRINCIPAL
                    </span>
            </div>

            {{-- Navigation --}}
            <nav class="app-navigation">

                {{-- Home --}}
                <a
                    href="{{ route('home') }}"
                    class="sidebar-link {{ request()->routeIs('home') ? 'active' : '' }}">

                        <span class="material-symbols-outlined sidebar-icon">
                            home
                        </span>

                    <span>Inicio</span>

                </a>

                {{-- Dashboard --}}
                <a
                    href="{{ route('dashboard') }}"
                    class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">

                        <span class="material-symbols-outlined sidebar-icon">
                            dashboard
                        </span>

                    <span>Dashboard</span>

                </a>

                {{-- Dynamic modules --}}
                @foreach ($menuModulos as $menuModulo)

                    @php

                        /*
                        |--------------------------------------------------------------------------
                        | Filtrar rutas y permisos
                        |--------------------------------------------------------------------------
                        */

                        $menuEnlaces = array_values(array_filter(
                            $menuModulo['enlaces'],

                            function ($menuEnlace) use ($rolUsuario) {

                                return
                                    \Illuminate\Support\Facades\Route::has($menuEnlace['ruta'])
                                    &&
                                    in_array(
                                        $rolUsuario,
                                        $menuEnlace['roles'],
                                        true
                                    );
                            }
                        ));

                        /*
                        |--------------------------------------------------------------------------
                        | Determinar estado activo del módulo
                        |--------------------------------------------------------------------------
                        */

                        $menuActivo = false;

                        foreach ($menuEnlaces as $menuEnlace) {

                            $patrones = [
                                $menuEnlace['ruta'],
                                $menuEnlace['ruta'] . '.*',
                            ];

                            foreach ($menuEnlace['rutas_activas'] ?? [] as $rutaActiva) {

                                $patrones[] = $rutaActiva;
                                $patrones[] = $rutaActiva . '.*';

                            }

                            if (request()->routeIs(...$patrones)) {
                                $menuActivo = true;
                                break;
                            }
                        }

                    @endphp

                    @continue($menuEnlaces === [])

                    {{-- Module group --}}
                    <div class="sidebar-module">

                        <button
                            type="button"
                            class="sidebar-link sidebar-group-toggle
                                       {{ $menuActivo ? 'has-active' : 'collapsed' }}"

                            data-bs-toggle="collapse"
                            data-bs-target="#modulo-{{ $menuModulo['id'] }}"

                            aria-expanded="{{ $menuActivo ? 'true' : 'false' }}"
                            aria-controls="modulo-{{ $menuModulo['id'] }}">

                                <span class="material-symbols-outlined sidebar-icon">
                                    {{ $menuModulo['icono'] }}
                                </span>

                            <span class="sidebar-link-label">
                                    {{ $menuModulo['titulo'] }}
                                </span>

                            <span class="material-symbols-outlined sidebar-chevron">
                                    expand_more
                                </span>

                        </button>

                        {{-- Module submenu --}}
                        <div
                            class="collapse sidebar-submenu {{ $menuActivo ? 'show' : '' }}"
                            id="modulo-{{ $menuModulo['id'] }}">

                            @foreach ($menuEnlaces as $menuEnlace)

                                @php

                                    $patronesEnlace = [
                                        $menuEnlace['ruta'],
                                        $menuEnlace['ruta'] . '.*',
                                    ];

                                    foreach ($menuEnlace['rutas_activas'] ?? [] as $rutaActiva) {

                                        $patronesEnlace[] = $rutaActiva;
                                        $patronesEnlace[] = $rutaActiva . '.*';

                                    }

                                    $enlaceActivo = request()->routeIs(...$patronesEnlace);

                                @endphp

                                <a
                                    href="{{ route($menuEnlace['ruta']) }}"
                                    class="sidebar-link sidebar-sublink
                                               {{ $enlaceActivo ? 'active' : '' }}">

                                        <span class="material-symbols-outlined sidebar-icon">
                                            {{ $menuEnlace['icono'] }}
                                        </span>

                                    <span>
                                            {{ $menuEnlace['etiqueta'] }}
                                        </span>

                                </a>

                            @endforeach

                        </div>

                    </div>

                @endforeach

            </nav>

            {{-- Sidebar footer --}}
            <footer class="app-sidebar-footer">

                    <span class="app-sidebar-footer-brand">
                        HealthWare
                    </span>

                <span class="app-sidebar-footer-version">
                        Sistema de Gestión Clínica · v1.0.0
                    </span>

                <span class="app-sidebar-copyright">
                        &copy; {{ date('Y') }} HealthWare
                    </span>

            </footer>

        </div>

    </aside>


    {{-- =================================================
         INNER APPLICATION SCREEN
    ================================================== --}}

    <main class="app-screen">

        <div class="app-screen-content">

            @yield('contenido')

        </div>

    </main>

</div>


{{-- =====================================================
     REUSABLE COMPONENTS
====================================================== --}}

@include('components.toast')


{{-- =====================================================
     SCRIPTS
====================================================== --}}

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<script src="{{ asset('js/toast.js') }}"></script>

@stack('scripts')

</body>
</html>
