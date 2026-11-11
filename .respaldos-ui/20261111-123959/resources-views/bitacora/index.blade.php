{{-- HEALTHWARE-GENERADO: generado por generar-frontend.sh v1.0.0 · no mezclar con otros marcadores --}}
@extends('layouts.app_layout')

@section('titulo', 'Bitácora')

@push('estilos')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('css/bitacora.css') }}">
@endpush

@section('contenido')

    @php
        $rolActual = \Illuminate\Support\Str::of(auth()->user()?->rol?->rol ?? '')->ascii()->lower()->toString();
        $puedeCrear = false;
        $puedeEditar = false;
        $puedeEliminar = false;
        $lista = $bitacoras ?? collect();
        $fmtFecha = fn($v, $f = 'd/m/Y') => $v ? \Illuminate\Support\Carbon::parse($v)->format($f) : '—';
    @endphp

    <div class="bitacora-page">

        {{-- ENCABEZADO --}}
        <header class="bitacora-header">
            <div class="bitacora-heading">
                <div class="bitacora-heading-icon">
                    <span class="material-symbols-outlined">history</span>
                </div>
                <div>
                    <h1 class="bitacora-title">Bitácora del sistema</h1>
                    <p class="bitacora-subtitle">Trazabilidad de las operaciones realizadas.</p>
                </div>
            </div>
        </header>

        {{-- FILTROS DEL SERVIDOR (BitacoraController: evento, fecha_desde, fecha_hasta) --}}
        <form method="GET" action="{{ route('bitacora') }}" class="bitacora-server-filters">
            <div class="form-floating">
                <select name="evento" id="filtro_evento" class="form-select">
                    <option value="">Todos</option>
                    @foreach(($eventos ?? []) as $evento)
                        <option value="{{ $evento }}" @selected(request('evento') === $evento)>{{ str_replace('_', ' ', $evento) }}</option>
                    @endforeach
                </select>
                <label for="filtro_evento">Evento</label>
            </div>
            <div class="form-floating">
                <input type="date" name="fecha_desde" id="filtro_desde" class="form-control" placeholder="Desde" value="{{ request('fecha_desde') }}">
                <label for="filtro_desde">Desde</label>
            </div>
            <div class="form-floating">
                <input type="date" name="fecha_hasta" id="filtro_hasta" class="form-control" placeholder="Hasta" value="{{ request('fecha_hasta') }}">
                <label for="filtro_hasta">Hasta</label>
            </div>
            <button type="submit" class="btn btn-primary">
                <span class="material-symbols-outlined">filter_alt</span>
                Filtrar
            </button>
            <a href="{{ route('bitacora') }}" class="btn btn-tonal">Limpiar</a>
        </form>


        {{-- SUPERFICIE --}}
        <section class="bitacora-surface">

            <div class="bitacora-surface-header">
                <div>
                    <h2 class="bitacora-surface-title">Eventos registrados</h2>
                    <p class="bitacora-surface-description">Accesos y cambios, del más reciente al más antiguo.</p>
                </div>
                <div class="bitacora-record-indicator">
                    <span class="material-symbols-outlined">database</span>
                    <span>{{ $lista->count() }} registros</span>
                </div>
            </div>

            @if($lista->isEmpty())
                <div class="bitacora-empty">
                    <span class="material-symbols-outlined">history</span>
                    <h3>Aún no hay registros</h3>
                    <p>Cuando existan datos aparecerán aquí.</p>
                </div>
            @else
            <div class="bitacora-toolbar">
                <select class="form-select" data-filtro-columna="3" aria-label="Filtrar por evento">
                    <option value="">Evento: todos</option>
                </select>
                <select class="form-select" data-filtro-columna="4" aria-label="Filtrar por módulo">
                    <option value="">Módulo: todos</option>
                </select>
                <button type="button" class="btn btn-text bitacora-toolbar-clear" data-filtros-limpiar>Limpiar filtros</button>
            </div>

            <div class="table-responsive bitacora-table-container">
                <table id="tabla-bitacora" class="table bitacora-table align-middle mb-0" data-page-length="10" data-sin-acciones="1">
                    <thead>
                        <tr>
                            <th scope="col" class="bitacora-column-id">ID</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Usuario</th>
                            <th scope="col">Evento</th>
                            <th scope="col">Módulo</th>
                            <th scope="col">Registro</th>
                            <th scope="col">Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($lista as $item)
                        <tr>
                            <td class="bitacora-id">#{{ $item->id }}</td>
                            <td>{{ $fmtFecha($item->created_at, 'd/m/Y H:i') }}</td>
                            <td>
                              <div class="bitacora-identity">
                                <div class="bitacora-avatar"><span class="material-symbols-outlined">person</span></div>
                                <div class="bitacora-identity-data">
                                  <span class="bitacora-fullname" data-filtro-valor>{{ $item->usuario_nombre ?? 'Sistema' }}</span>
                                  <span class="bitacora-identity-caption">{{ \Illuminate\Support\Str::limit((string) ($item->ip_address ?? ""), 60) }}</span>
                                </div>
                              </div>
                            </td>
                            <td><span class="bitacora-status bitacora-status-{{ ['LOGIN_OK' => 'success', 'LOGIN_FALLIDO' => 'error', 'LOGOUT' => 'neutral', 'CREADO' => 'info', 'ACTUALIZADO' => 'warning', 'ELIMINADO' => 'error', ][(string) ($item->evento)] ?? "neutral" }}" data-filtro-valor><span class="bitacora-status-dot"></span>{{ ucfirst(str_replace("_", " ", (string) ($item->evento))) }}</span></td>
                            <td><span class="bitacora-chip" data-filtro-valor>{{ $item->modulo ?? "N/A" }}</span></td>
                            <td>{{ $item->registro_id ? '#' . $item->registro_id : null ?? "—" }}</td>
                            <td class="bitacora-muted" title="{{ $item->descripcion }}">{{ \Illuminate\Support\Str::limit((string) ($item->descripcion ?? ""), 70) ?: "—" }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif

        </section>
    </div>

    {{-- ===== ESPECIFICACIÓN ORIGINAL (conservada por generar-frontend.sh) ===== --}}
    {{--
    VISTA: BITÁCORA DE ACTIVIDADES
    ------------------------------------------------------------
    OBJETIVO:
    Proporcionar al administrador un registro de las actividades
    realizadas dentro del sistema, permitiendo consultar eventos
    importantes relacionados con los usuarios y las operaciones.

    CONTENIDO GENERAL:
    1. Encabezado:
       - Título "Bitácora de actividades".
       - Descripción de la utilidad de la sección.
       - No incluir botones de creación, edición o eliminación.

    2. Panel de filtros:
       - Campo de búsqueda por usuario.
       - Selector de tipo de evento.
       - Campo de fecha inicial.
       - Campo de fecha final.
       - Botón para aplicar filtros.
       - Botón para limpiar los filtros.

    3. Tabla de actividades:
       - Identificador del registro.
       - Usuario responsable, cuando exista.
       - Tipo de evento.
       - Descripción de la actividad.
       - Dirección IP, si está almacenada.
       - Fecha y hora del evento.
       - Acción para consultar los detalles, si corresponde.

    4. Identificación visual de eventos:
       - Utilizar badges para distinguir los tipos de actividad.
       - Mantener colores consistentes para eventos de autenticación,
         creación, actualización y eliminación lógica.
       - No depender exclusivamente del color para identificar
         el tipo de evento; conservar también su etiqueta textual.

    5. Modal de detalles:
       - Mostrar el identificador del registro.
       - Usuario relacionado.
       - Tipo de evento.
       - Descripción completa.
       - Dirección IP, si existe.
       - Fecha y hora.
       - Información adicional almacenada, si corresponde.

    REGLAS DE NEGOCIO:
    - La bitácora es exclusivamente de consulta.
    - No permitir modificar ni eliminar registros desde esta vista.
    - Los registros deben conservar su integridad histórica.
    - El acceso está restringido al administrador.
    - Los registros pueden no tener un usuario asociado, por ejemplo,
      ante determinados eventos de autenticación.
    - Los filtros deben aplicarse desde el backend para evitar
      cargar innecesariamente todos los registros.

    INTEGRACIÓN:
    - Utilizar BitacoraController.
    - Consultar los registros mediante el método index.
    - Los filtros deben enviarse mediante GET.
    - Mantener la paginación y conservar los filtros aplicados.
    - No exponer información sensible almacenada en los registros.
--}}
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('js/bitacora/bitacora.js') }}"></script>
@endpush
