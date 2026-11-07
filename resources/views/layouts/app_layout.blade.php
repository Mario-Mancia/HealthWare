
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
    <link rel="stylesheet" href="{{ asset('css/sidebar-modulos.css') }}">

    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    @stack('estilos')
</head>

<body class="d-flex flex-column">

{{-- TOPBAR --}}
<header class="app-topbar d-flex align-items-center px-3 px-lg-4">

    {{-- Menú móvil --}}
    <button
        class="btn btn-link text-inherit d-lg-none me-2 p-0"
        style="color: inherit;"
        type="button"
        data-bs-toggle="offcanvas"
        data-bs-target="#sidebarOffcanvas"
        aria-controls="sidebarOffcanvas"
    >
        <span class="material-symbols-outlined fs-2">menu</span>
    </button>

    {{-- Branding --}}
    <a
        class="navbar-brand d-flex align-items-center fw-bold gap-2"
        style="color: inherit;"
        href="{{ route('home') }}"
    >
        <span class="material-symbols-outlined fs-2">health_and_safety</span>
        <span class="fs-4">HealthWare</span>
    </a>

    <div class="ms-auto"></div>

    {{-- Usuario --}}
    <div class="dropdown">
        <button
            class="btn btn-link text-decoration-none d-flex align-items-center gap-2 p-0"
            style="color: inherit;"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
        >
            <div class="user-avatar rounded-circle d-flex align-items-center justify-content-center shadow-sm">
                <span class="material-symbols-outlined">person</span>
            </div>

            <span class="d-none d-md-block fw-medium">
                {{ Auth::user()->nombres ?? 'Usuario' }}
            </span>

            <span class="material-symbols-outlined fs-5 d-none d-md-block opacity-75">
                expand_more
            </span>
        </button>

        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2 rounded-3">

            <li>
                <div class="dropdown-item-text px-3 py-2">
                    <div class="fw-semibold">
                        {{ Auth::user()->nombres ?? 'Usuario' }}
                    </div>
                    <small class="text-body-secondary">
                        {{ Auth::user()->rol->rol ?? '' }}
                    </small>
                </div>
            </li>

            <li><hr class="dropdown-divider opacity-10"></li>

            <li>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf

                    <button type="submit" class="dropdown-item btn-soft-danger">
                        <span class="material-symbols-outlined fs-5">logout</span>
                        Cerrar sesión
                    </button>
                </form>
            </li>

        </ul>
    </div>
</header>


{{-- CONTENEDOR PRINCIPAL --}}
<div class="d-flex flex-grow-1 overflow-hidden">

    {{-- SIDEBAR --}}
    <aside
        class="offcanvas-lg offcanvas-start app-sidebar px-3 pb-3"
        tabindex="-1"
        id="sidebarOffcanvas"
    >

        <div class="offcanvas-header d-lg-none">
            <h5
                class="offcanvas-title fw-bold d-flex align-items-center gap-2"
                style="color: var(--md-sys-color-on-brand-bg);"
            >
                <span class="material-symbols-outlined">health_and_safety</span>
                HealthWare
            </h5>

            <button
                type="button"
                class="btn-close"
                style="filter: invert(1) grayscale(100%) brightness(200%);"
                data-bs-dismiss="offcanvas"
                data-bs-target="#sidebarOffcanvas"
            ></button>
        </div>

        <div class="offcanvas-body flex-column p-0 pt-lg-2 h-100">

            <h6
                class="text-uppercase fw-bold px-3 mb-2 mt-2"
                style="font-size: 0.7rem; letter-spacing: 1px; opacity: 0.6;"
            >
                Menú principal
            </h6>

            @php
                /*
                |--------------------------------------------------------------------------
                | Rol del usuario autenticado
                |--------------------------------------------------------------------------
                */

                $rolUsuario = mb_strtolower(
                    trim(Auth::user()->rol->rol ?? '')
                );

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
                | Configuración de módulos y enlaces
                |--------------------------------------------------------------------------
                */

                $menuModulos = [

                    /*
                    |--------------------------------------------------------------------------
                    | Pacientes
                    |--------------------------------------------------------------------------
                    */

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

                    /*
                    |--------------------------------------------------------------------------
                    | Atención médica
                    |--------------------------------------------------------------------------
                    */

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

                    /*
                    |--------------------------------------------------------------------------
                    | Personal médico
                    |--------------------------------------------------------------------------
                    */

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

                    /*
                    |--------------------------------------------------------------------------
                    | Facturación
                    |--------------------------------------------------------------------------
                    */

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

                    /*
                    |--------------------------------------------------------------------------
                    | Comunicación
                    |--------------------------------------------------------------------------
                    */

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

                    /*
                    |--------------------------------------------------------------------------
                    | Catálogos clínicos
                    |--------------------------------------------------------------------------
                    */

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

                    /*
                    |--------------------------------------------------------------------------
                    | Administración
                    |--------------------------------------------------------------------------
                    */

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


            {{-- NAVEGACIÓN --}}
            <nav class="d-flex flex-column flex-grow-1 gap-1">

                {{-- Inicio --}}
                <a
                    href="{{ route('home') }}"
                    class="sidebar-link {{ request()->routeIs('home') ? 'active' : '' }}"
                >
                    <span class="material-symbols-outlined me-2">home</span>
                    Inicio
                </a>

                {{-- Dashboard --}}
                <a
                    href="{{ route('dashboard') }}"
                    class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                >
                    <span class="material-symbols-outlined me-2">dashboard</span>
                    Dashboard
                </a>


                {{-- Módulos dinámicos --}}
                @foreach ($menuModulos as $menuModulo)

                    @php
                        /*
                        | Filtrar enlaces según:
                        | 1. Existencia de la ruta.
                        | 2. Rol del usuario.
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
                        | Determinar si algún enlace del módulo está activo.
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


                    {{-- No mostrar módulos sin enlaces disponibles --}}
                    @continue($menuEnlaces === [])


                    {{-- Botón del módulo --}}
                    <button
                        type="button"
                        class="sidebar-link sidebar-group-toggle {{ $menuActivo ? 'has-active' : 'collapsed' }}"
                        data-bs-toggle="collapse"
                        data-bs-target="#modulo-{{ $menuModulo['id'] }}"
                        aria-expanded="{{ $menuActivo ? 'true' : 'false' }}"
                        aria-controls="modulo-{{ $menuModulo['id'] }}"
                    >
                        <span class="material-symbols-outlined me-2">
                            {{ $menuModulo['icono'] }}
                        </span>

                        {{ $menuModulo['titulo'] }}

                        <span class="material-symbols-outlined ms-auto sidebar-chevron">
                            expand_more
                        </span>
                    </button>


                    {{-- Submenú --}}
                    <div
                        class="collapse sidebar-submenu {{ $menuActivo ? 'show' : '' }}"
                        id="modulo-{{ $menuModulo['id'] }}"
                    >

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
                                class="sidebar-link sidebar-sublink {{ $enlaceActivo ? 'active' : '' }}"
                            >
                                <span class="material-symbols-outlined me-2">
                                    {{ $menuEnlace['icono'] }}
                                </span>

                                {{ $menuEnlace['etiqueta'] }}
                            </a>

                        @endforeach

                    </div>

                @endforeach

            </nav>


            {{-- FOOTER SIDEBAR --}}
            <div
                class="mt-auto px-3 text-center"
                style="font-size: 0.75rem; opacity: 0.6;"
            >
                &copy; {{ date('Y') }} HealthWare.
                <br>
                Versión 1.0.0
            </div>

        </div>
    </aside>


    {{-- CONTENIDO PRINCIPAL --}}
    <main class="app-screen d-flex flex-column flex-grow-1 position-relative p-4">
        @yield('contenido')
    </main>

</div>


{{-- NOTIFICACIONES --}}
@include('components.toast')


{{-- SCRIPTS GLOBALES --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/toast.js') }}"></script>

@stack('scripts')

</body>
</html>
