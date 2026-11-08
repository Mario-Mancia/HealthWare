{{-- HEALTHWARE-GENERADO: generado por generar-frontend.sh v1.0.0 · no mezclar con otros marcadores --}}
@extends('layouts.app_layout')

@section('titulo', 'Estados')

@push('estilos')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('css/estados.css') }}">
@endpush

@section('contenido')

    @php
        $rolActual = \Illuminate\Support\Str::of(auth()->user()?->rol?->rol ?? '')->ascii()->lower()->toString();
        $puedeCrear = in_array($rolActual, ['administrador']);
        $puedeEditar = in_array($rolActual, ['administrador']);
        $puedeEliminar = in_array($rolActual, ['administrador']);
        $lista = $estados ?? collect();
        $fmtFecha = fn($v, $f = 'd/m/Y') => $v ? \Illuminate\Support\Carbon::parse($v)->format($f) : '—';
    @endphp

    <div class="estados-page">

        {{-- ENCABEZADO --}}
        <header class="estados-header">
            <div class="estados-heading">
                <div class="estados-heading-icon">
                    <span class="material-symbols-outlined">toggle_on</span>
                </div>
                <div>
                    <h1 class="estados-title">Estados del sistema</h1>
                    <p class="estados-subtitle">Estados que determinan la situación de las cuentas.</p>
                </div>
            </div>

            @if($puedeCrear)
                <button type="button"
                        class="btn btn-primary estados-create-button"
                        data-bs-toggle="modal"
                        data-bs-target="#modalNuevo">
                    <span class="material-symbols-outlined">add_circle</span>
                    Nuevo estado
                </button>
            @endif
        </header>


        {{-- SUPERFICIE --}}
        <section class="estados-surface">

            <div class="estados-surface-header">
                <div>
                    <h2 class="estados-surface-title">Estados registrados</h2>
                    <p class="estados-surface-description">Consulta y administra los estados disponibles.</p>
                </div>
                <div class="estados-record-indicator">
                    <span class="material-symbols-outlined">database</span>
                    <span>{{ $lista->count() }} registros</span>
                </div>
            </div>

            @if($lista->isEmpty())
                <div class="estados-empty">
                    <span class="material-symbols-outlined">toggle_on</span>
                    <h3>Aún no hay registros</h3>
                    <p>Cuando existan datos aparecerán aquí.</p>
                </div>
            @else
            <div class="table-responsive estados-table-container">
                <table id="tabla-estados" class="table estados-table align-middle mb-0" data-page-length="10">
                    <thead>
                        <tr>
                            <th scope="col" class="estados-column-id">ID</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Descripción</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($lista as $item)
                        <tr>
                            <td class="estados-id">#{{ $item->id }}</td>
                            <td>
                              <div class="estados-identity">
                                <div class="estados-avatar"><span class="material-symbols-outlined">toggle_on</span></div>
                                <div class="estados-identity-data">
                                  <span class="estados-fullname" data-filtro-valor>{{ $item->estado }}</span>
                                </div>
                              </div>
                            </td>
                            <td class="estados-muted" title="{{ $item->descripcion }}">{{ \Illuminate\Support\Str::limit((string) ($item->descripcion ?? ""), 70) ?: "—" }}</td>
                            <td>
                                <div class="estados-actions">
                                    <button type="button" class="estados-action-button"
                                            data-bs-toggle="modal" data-bs-target="#modalVer"
                                            data-id="{{ $item->id }}"
                                            title="Consultar estado" aria-label="Consultar estado">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </button>
                                    @if($puedeEditar)
                                        <button type="button" class="estados-action-button"
                                                data-bs-toggle="modal" data-bs-target="#modalEditar"
                                                data-id="{{ $item->id }}"
                                                title="Editar estado" aria-label="Editar estado">
                                            <span class="material-symbols-outlined">edit</span>
                                        </button>
                                    @endif
                                    @if($puedeEliminar)
                                        <button type="button" class="estados-action-button estados-action-delete"
                                                data-bs-toggle="modal" data-bs-target="#modalEliminar"
                                                data-id="{{ $item->id }}"
                                                data-nombre="{{ $item->estado }}"
                                                data-sub="{{ $item->descripcion ?? "" }}"
                                                title="Eliminar estado" aria-label="Eliminar estado">
                                            <span class="material-symbols-outlined">delete</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif

        </section>
    </div>

    {{-- MODAL: NUEVO --}}
    @if($puedeCrear)
    <div class="modal fade estados-modal" id="modalNuevo" tabindex="-1" aria-labelledby="tituloNuevo" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="estados-modal-header">
                    <div class="estados-modal-heading">
                        <div class="estados-modal-icon"><span class="material-symbols-outlined">add_circle</span></div>
                        <div>
                            <h2 class="estados-modal-title" id="tituloNuevo">Registrar estado</h2>
                            <p class="estados-modal-subtitle">Completa la información solicitada.</p>
                        </div>
                    </div>
                    <button type="button" class="estados-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="formNuevo" method="POST" action="{{ route('estados.insertar') }}" novalidate>
                    @csrf
                    <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                    <div class="modal-body estados-modal-body">
                        <div class="estados-form-section">
                          <div class="estados-form-section-heading">
                            <span class="material-symbols-outlined">toggle_on</span>
                            <div>
                              <h3>Información del registro</h3>
                              <p>Datos que identifican este elemento del catálogo.</p>
                            </div>
                          </div>
                          <div class="row g-3">
                            <div class="col-md-12">
                              <div class="form-floating">
                                <input type="text" name="estado" id="nuevo_estado" class="form-control" placeholder="Estado" maxlength="50" required>
                                <label for="nuevo_estado">Estado</label>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="form-floating">
                                <textarea name="descripcion" id="nuevo_descripcion" class="form-control" placeholder="Descripción" maxlength="250"></textarea>
                                <label for="nuevo_descripcion">Descripción</label>
                              </div>
                              <div class="estados-field-hint">Máximo 250 caracteres.</div>
                            </div>
                          </div>
                        </div>
                    </div>

                    <div class="estados-modal-footer">
                        <button type="button" class="btn btn-tonal" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="material-symbols-outlined">save</span>
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL: EDITAR --}}
    @if($puedeEditar)
    <div class="modal fade estados-modal" id="modalEditar" tabindex="-1" aria-labelledby="tituloEditar" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="estados-modal-header">
                    <div class="estados-modal-heading">
                        <div class="estados-modal-icon"><span class="material-symbols-outlined">edit</span></div>
                        <div>
                            <h2 class="estados-modal-title" id="tituloEditar">Editar estado #<span id="editarTituloId">—</span></h2>
                            <p class="estados-modal-subtitle">Actualiza la información y guarda los cambios.</p>
                        </div>
                    </div>
                    <button type="button" class="estados-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="formEditar" method="POST"
                      data-url="{{ route('estados.actualizar', ['estado' => '__ID__']) }}"
                      data-buscar-url="{{ route('estados.buscar', ['id' => '__ID__']) }}"
                      novalidate>
                    @csrf
                    @method('PUT')
                    <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                    <div class="modal-body estados-modal-body">
                        <div class="estados-form-section">
                          <div class="estados-form-section-heading">
                            <span class="material-symbols-outlined">toggle_on</span>
                            <div>
                              <h3>Información del registro</h3>
                              <p>Datos que identifican este elemento del catálogo.</p>
                            </div>
                          </div>
                          <div class="row g-3">
                            <div class="col-md-12">
                              <div class="form-floating">
                                <input type="text" name="estado" id="editar_estado" class="form-control" placeholder="Estado" maxlength="50" required>
                                <label for="editar_estado">Estado</label>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="form-floating">
                                <textarea name="descripcion" id="editar_descripcion" class="form-control" placeholder="Descripción" maxlength="250"></textarea>
                                <label for="editar_descripcion">Descripción</label>
                              </div>
                              <div class="estados-field-hint">Máximo 250 caracteres.</div>
                            </div>
                          </div>
                        </div>
                    </div>

                    <div class="estados-modal-footer">
                        <button type="button" class="btn btn-tonal" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="material-symbols-outlined">save</span>
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL: CONSULTA --}}
    <div class="modal fade estados-modal" id="modalVer" tabindex="-1" aria-labelledby="tituloVer" aria-hidden="true"
         data-buscar-url="{{ route('estados.buscar', ['id' => '__ID__']) }}">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="estados-modal-header">
                    <div class="estados-modal-heading">
                        <div class="estados-modal-icon"><span class="material-symbols-outlined">visibility</span></div>
                        <div>
                            <h2 class="estados-modal-title" id="tituloVer">Detalle de estado</h2>
                            <p class="estados-modal-subtitle">Información completa del registro.</p>
                        </div>
                    </div>
                    <button type="button" class="estados-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                <div class="modal-body estados-modal-body">
                    <dl class="estados-detail-grid">
                        <div class="estados-detail-item"><dt>Identificador</dt><dd data-ver="id">—</dd></div>
                        <div class="estados-detail-item"><dt>Estado</dt><dd data-ver="estado">—</dd></div>
                        <div class="estados-detail-item is-wide"><dt>Descripción</dt><dd data-ver="descripcion">—</dd></div>
                    </dl>
                </div>

                <div class="estados-modal-footer">
                    <button type="button" class="btn btn-tonal" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: ELIMINAR --}}
    @if($puedeEliminar)
    <div class="modal fade estados-modal estados-delete-modal" id="modalEliminar" tabindex="-1" aria-labelledby="tituloEliminar" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="estados-delete-content">
                    <div class="estados-delete-icon"><span class="material-symbols-outlined">delete</span></div>

                    <button type="button" class="estados-modal-close estados-delete-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>

                    <div class="estados-delete-heading">
                        <span class="estados-delete-overline">Confirmación requerida</span>
                        <h2 id="tituloEliminar">¿Eliminar este estado?</h2>
                        <p>Esta acción no se puede deshacer desde la interfaz.</p>
                    </div>

                    <form id="formEliminar" method="POST"
                          data-url="{{ route('estados.eliminar', ['estado' => '__ID__']) }}" novalidate>
                        @csrf
                        @method('DELETE')
                        <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                        <div class="estados-delete-identity">
                            <div class="estados-delete-avatar"><span class="material-symbols-outlined">toggle_on</span></div>
                            <div class="estados-delete-data">
                                <span class="estados-delete-name" id="eliminar_nombre">—</span>
                                <span class="estados-delete-sub" id="eliminar_sub">—</span>
                                <span class="estados-delete-id">Identificador: #<span id="eliminar_id">—</span></span>
                            </div>
                        </div>

                        <div class="estados-delete-notice">
                            <span class="material-symbols-outlined">info</span>
                            <p>Los usuarios dependen de estos estados (1 Activo, 2 Inactivo, 3 Eliminado). No elimines uno que esté en uso.</p>
                        </div>

                        <div class="estados-delete-actions">
                            <button type="button" class="btn btn-tonal" data-bs-dismiss="modal">Conservar</button>
                            <button type="submit" class="btn estados-delete-confirm">
                                <span class="material-symbols-outlined">delete</span>
                                Eliminar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== ESPECIFICACIÓN ORIGINAL (conservada por generar-frontend.sh) ===== --}}
    {{--
        VISTA: Administración de estados.

        OBJETIVO:
        Gestionar los estados generales utilizados por las entidades
        que dependen de este catálogo.

        CONTENIDO:
        - Encabezado.
        - Botón para registrar estado.
        - Tabla con identificador, nombre y descripción.
        - Búsqueda por nombre.
        - Modal de creación.
        - Modal de edición.
        - Confirmación de eliminación.

        INTERACCIÓN:
        - Crear, consultar, actualizar y eliminar estados.
        - Mostrar notificaciones de operaciones.

        CONSIDERACIONES:
        - El catálogo se utiliza principalmente para el estado de los usuarios.
        - No confundir estos estados con los estados específicos de citas,
          facturas u otras entidades que utilizan valores propios.
        - Evitar eliminar estados que estén siendo utilizados.
        - La administración corresponde exclusivamente al administrador.
    --}}
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('js/estado/estados.js') }}"></script>
@endpush
