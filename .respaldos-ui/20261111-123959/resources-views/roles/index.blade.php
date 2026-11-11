{{-- HEALTHWARE-GENERADO: generado por generar-frontend.sh v1.0.0 · no mezclar con otros marcadores --}}
@extends('layouts.app_layout')

@section('titulo', 'Roles')

@push('estilos')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('css/roles.css') }}">
@endpush

@section('contenido')

    @php
        $rolActual = \Illuminate\Support\Str::of(auth()->user()?->rol?->rol ?? '')->ascii()->lower()->toString();
        $puedeCrear = in_array($rolActual, ['administrador']);
        $puedeEditar = in_array($rolActual, ['administrador']);
        $puedeEliminar = in_array($rolActual, ['administrador']);
        $lista = $roles ?? collect();
        $fmtFecha = fn($v, $f = 'd/m/Y') => $v ? \Illuminate\Support\Carbon::parse($v)->format($f) : '—';
    @endphp

    <div class="roles-page">

        {{-- ENCABEZADO --}}
        <header class="roles-header">
            <div class="roles-heading">
                <div class="roles-heading-icon">
                    <span class="material-symbols-outlined">admin_panel_settings</span>
                </div>
                <div>
                    <h1 class="roles-title">Roles del sistema</h1>
                    <p class="roles-subtitle">Niveles de acceso de los usuarios de HealthWare.</p>
                </div>
            </div>

            @if($puedeCrear)
                <button type="button"
                        class="btn btn-primary roles-create-button"
                        data-bs-toggle="modal"
                        data-bs-target="#modalNuevo">
                    <span class="material-symbols-outlined">add_circle</span>
                    Nuevo rol
                </button>
            @endif
        </header>


        {{-- SUPERFICIE --}}
        <section class="roles-surface">

            <div class="roles-surface-header">
                <div>
                    <h2 class="roles-surface-title">Roles registrados</h2>
                    <p class="roles-surface-description">Consulta y administra los roles disponibles.</p>
                </div>
                <div class="roles-record-indicator">
                    <span class="material-symbols-outlined">database</span>
                    <span>{{ $lista->count() }} registros</span>
                </div>
            </div>

            @if($lista->isEmpty())
                <div class="roles-empty">
                    <span class="material-symbols-outlined">admin_panel_settings</span>
                    <h3>Aún no hay registros</h3>
                    <p>Cuando existan datos aparecerán aquí.</p>
                </div>
            @else
            <div class="table-responsive roles-table-container">
                <table id="tabla-roles" class="table roles-table align-middle mb-0" data-page-length="10">
                    <thead>
                        <tr>
                            <th scope="col" class="roles-column-id">ID</th>
                            <th scope="col">Rol</th>
                            <th scope="col">Descripción</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($lista as $item)
                        <tr>
                            <td class="roles-id">#{{ $item->id }}</td>
                            <td>
                              <div class="roles-identity">
                                <div class="roles-avatar"><span class="material-symbols-outlined">admin_panel_settings</span></div>
                                <div class="roles-identity-data">
                                  <span class="roles-fullname" data-filtro-valor>{{ $item->rol }}</span>
                                </div>
                              </div>
                            </td>
                            <td class="roles-muted" title="{{ $item->descripcion }}">{{ \Illuminate\Support\Str::limit((string) ($item->descripcion ?? ""), 70) ?: "—" }}</td>
                            <td>
                                <div class="roles-actions">
                                    <button type="button" class="roles-action-button"
                                            data-bs-toggle="modal" data-bs-target="#modalVer"
                                            data-id="{{ $item->id }}"
                                            title="Consultar rol" aria-label="Consultar rol">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </button>
                                    @if($puedeEditar)
                                        <button type="button" class="roles-action-button"
                                                data-bs-toggle="modal" data-bs-target="#modalEditar"
                                                data-id="{{ $item->id }}"
                                                title="Editar rol" aria-label="Editar rol">
                                            <span class="material-symbols-outlined">edit</span>
                                        </button>
                                    @endif
                                    @if($puedeEliminar)
                                        <button type="button" class="roles-action-button roles-action-delete"
                                                data-bs-toggle="modal" data-bs-target="#modalEliminar"
                                                data-id="{{ $item->id }}"
                                                data-nombre="{{ $item->rol }}"
                                                data-sub="{{ $item->descripcion ?? "" }}"
                                                title="Eliminar rol" aria-label="Eliminar rol">
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
    <div class="modal fade roles-modal" id="modalNuevo" tabindex="-1" aria-labelledby="tituloNuevo" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="roles-modal-header">
                    <div class="roles-modal-heading">
                        <div class="roles-modal-icon"><span class="material-symbols-outlined">add_circle</span></div>
                        <div>
                            <h2 class="roles-modal-title" id="tituloNuevo">Registrar rol</h2>
                            <p class="roles-modal-subtitle">Completa la información solicitada.</p>
                        </div>
                    </div>
                    <button type="button" class="roles-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="formNuevo" method="POST" action="{{ route('roles.insertar') }}" novalidate>
                    @csrf
                    <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                    <div class="modal-body roles-modal-body">
                        <div class="roles-form-section">
                          <div class="roles-form-section-heading">
                            <span class="material-symbols-outlined">admin_panel_settings</span>
                            <div>
                              <h3>Información del registro</h3>
                              <p>Datos que identifican este elemento del catálogo.</p>
                            </div>
                          </div>
                          <div class="row g-3">
                            <div class="col-md-12">
                              <div class="form-floating">
                                <input type="text" name="rol" id="nuevo_rol" class="form-control" placeholder="Rol" maxlength="50" required>
                                <label for="nuevo_rol">Rol</label>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="form-floating">
                                <textarea name="descripcion" id="nuevo_descripcion" class="form-control" placeholder="Descripción" maxlength="250"></textarea>
                                <label for="nuevo_descripcion">Descripción</label>
                              </div>
                              <div class="roles-field-hint">Máximo 250 caracteres.</div>
                            </div>
                          </div>
                        </div>
                    </div>

                    <div class="roles-modal-footer">
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
    <div class="modal fade roles-modal" id="modalEditar" tabindex="-1" aria-labelledby="tituloEditar" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="roles-modal-header">
                    <div class="roles-modal-heading">
                        <div class="roles-modal-icon"><span class="material-symbols-outlined">edit</span></div>
                        <div>
                            <h2 class="roles-modal-title" id="tituloEditar">Editar rol #<span id="editarTituloId">—</span></h2>
                            <p class="roles-modal-subtitle">Actualiza la información y guarda los cambios.</p>
                        </div>
                    </div>
                    <button type="button" class="roles-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="formEditar" method="POST"
                      data-url="{{ route('roles.actualizar', ['rol' => '__ID__']) }}"
                      data-buscar-url="{{ route('roles.buscar', ['id' => '__ID__']) }}"
                      novalidate>
                    @csrf
                    @method('PUT')
                    <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                    <div class="modal-body roles-modal-body">
                        <div class="roles-form-section">
                          <div class="roles-form-section-heading">
                            <span class="material-symbols-outlined">admin_panel_settings</span>
                            <div>
                              <h3>Información del registro</h3>
                              <p>Datos que identifican este elemento del catálogo.</p>
                            </div>
                          </div>
                          <div class="row g-3">
                            <div class="col-md-12">
                              <div class="form-floating">
                                <input type="text" name="rol" id="editar_rol" class="form-control" placeholder="Rol" maxlength="50" required>
                                <label for="editar_rol">Rol</label>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="form-floating">
                                <textarea name="descripcion" id="editar_descripcion" class="form-control" placeholder="Descripción" maxlength="250"></textarea>
                                <label for="editar_descripcion">Descripción</label>
                              </div>
                              <div class="roles-field-hint">Máximo 250 caracteres.</div>
                            </div>
                          </div>
                        </div>
                    </div>

                    <div class="roles-modal-footer">
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
    <div class="modal fade roles-modal" id="modalVer" tabindex="-1" aria-labelledby="tituloVer" aria-hidden="true"
         data-buscar-url="{{ route('roles.buscar', ['id' => '__ID__']) }}">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="roles-modal-header">
                    <div class="roles-modal-heading">
                        <div class="roles-modal-icon"><span class="material-symbols-outlined">visibility</span></div>
                        <div>
                            <h2 class="roles-modal-title" id="tituloVer">Detalle de rol</h2>
                            <p class="roles-modal-subtitle">Información completa del registro.</p>
                        </div>
                    </div>
                    <button type="button" class="roles-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                <div class="modal-body roles-modal-body">
                    <dl class="roles-detail-grid">
                        <div class="roles-detail-item"><dt>Identificador</dt><dd data-ver="id">—</dd></div>
                        <div class="roles-detail-item"><dt>Rol</dt><dd data-ver="rol">—</dd></div>
                        <div class="roles-detail-item is-wide"><dt>Descripción</dt><dd data-ver="descripcion">—</dd></div>
                    </dl>
                </div>

                <div class="roles-modal-footer">
                    <button type="button" class="btn btn-tonal" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: ELIMINAR --}}
    @if($puedeEliminar)
    <div class="modal fade roles-modal roles-delete-modal" id="modalEliminar" tabindex="-1" aria-labelledby="tituloEliminar" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="roles-delete-content">
                    <div class="roles-delete-icon"><span class="material-symbols-outlined">delete</span></div>

                    <button type="button" class="roles-modal-close roles-delete-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>

                    <div class="roles-delete-heading">
                        <span class="roles-delete-overline">Confirmación requerida</span>
                        <h2 id="tituloEliminar">¿Eliminar este rol?</h2>
                        <p>Esta acción no se puede deshacer desde la interfaz.</p>
                    </div>

                    <form id="formEliminar" method="POST"
                          data-url="{{ route('roles.eliminar', ['rol' => '__ID__']) }}" novalidate>
                        @csrf
                        @method('DELETE')
                        <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                        <div class="roles-delete-identity">
                            <div class="roles-delete-avatar"><span class="material-symbols-outlined">admin_panel_settings</span></div>
                            <div class="roles-delete-data">
                                <span class="roles-delete-name" id="eliminar_nombre">—</span>
                                <span class="roles-delete-sub" id="eliminar_sub">—</span>
                                <span class="roles-delete-id">Identificador: #<span id="eliminar_id">—</span></span>
                            </div>
                        </div>

                        <div class="roles-delete-notice">
                            <span class="material-symbols-outlined">info</span>
                            <p>No elimines roles asignados a usuarios: quedarían sin un rol válido.</p>
                        </div>

                        <div class="roles-delete-actions">
                            <button type="button" class="btn btn-tonal" data-bs-dismiss="modal">Conservar</button>
                            <button type="submit" class="btn roles-delete-confirm">
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
        VISTA: Administración de roles del sistema.

        OBJETIVO:
        Gestionar los roles que determinan los niveles de acceso
        de los usuarios de HealthWare.

        CONTENIDO:
        - Encabezado.
        - Botón para registrar un rol.
        - Tabla con identificador, nombre del rol y descripción.
        - Búsqueda por nombre.
        - Modal de creación.
        - Modal de edición.
        - Confirmación de eliminación.

        FORMULARIO:
        - Nombre del rol.
        - Descripción.

        INTERACCIÓN:
        - Crear, consultar, actualizar y eliminar roles.
        - Presentar notificaciones de éxito o error.

        CONSIDERACIONES:
        - Los roles actuales son Administrador, Medico y Recepcionista.
        - Evitar eliminar roles asignados a usuarios.
        - No permitir operaciones que dejen usuarios sin un rol válido.
        - La administración es exclusiva del administrador.
        - Los permisos efectivos se controlan mediante middleware y
          las políticas de autorización correspondientes.
    --}}
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('js/rol/roles.js') }}"></script>
@endpush
