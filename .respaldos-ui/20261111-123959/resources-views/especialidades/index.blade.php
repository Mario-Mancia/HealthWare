{{-- HEALTHWARE-GENERADO: generado por generar-frontend.sh v1.0.0 · no mezclar con otros marcadores --}}
@extends('layouts.app_layout')

@section('titulo', 'Especialidades')

@push('estilos')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('css/especialidades.css') }}">
@endpush

@section('contenido')

    @php
        $rolActual = \Illuminate\Support\Str::of(auth()->user()?->rol?->rol ?? '')->ascii()->lower()->toString();
        $puedeCrear = in_array($rolActual, ['administrador']);
        $puedeEditar = in_array($rolActual, ['administrador']);
        $puedeEliminar = in_array($rolActual, ['administrador']);
        $lista = $especialidades ?? collect();
        $fmtFecha = fn($v, $f = 'd/m/Y') => $v ? \Illuminate\Support\Carbon::parse($v)->format($f) : '—';
    @endphp

    <div class="especialidades-page">

        {{-- ENCABEZADO --}}
        <header class="especialidades-header">
            <div class="especialidades-heading">
                <div class="especialidades-heading-icon">
                    <span class="material-symbols-outlined">workspace_premium</span>
                </div>
                <div>
                    <h1 class="especialidades-title">Especialidades médicas</h1>
                    <p class="especialidades-subtitle">Especialidades que pueden asignarse a los doctores.</p>
                </div>
            </div>

            @if($puedeCrear)
                <button type="button"
                        class="btn btn-primary especialidades-create-button"
                        data-bs-toggle="modal"
                        data-bs-target="#modalNuevo">
                    <span class="material-symbols-outlined">add_circle</span>
                    Nueva especialidad
                </button>
            @endif
        </header>


        {{-- SUPERFICIE --}}
        <section class="especialidades-surface">

            <div class="especialidades-surface-header">
                <div>
                    <h2 class="especialidades-surface-title">Especialidades registradas</h2>
                    <p class="especialidades-surface-description">Consulta y administra el catálogo de especialidades.</p>
                </div>
                <div class="especialidades-record-indicator">
                    <span class="material-symbols-outlined">database</span>
                    <span>{{ $lista->count() }} registros</span>
                </div>
            </div>

            @if($lista->isEmpty())
                <div class="especialidades-empty">
                    <span class="material-symbols-outlined">workspace_premium</span>
                    <h3>Aún no hay registros</h3>
                    <p>Cuando existan datos aparecerán aquí.</p>
                </div>
            @else
            <div class="table-responsive especialidades-table-container">
                <table id="tabla-especialidades" class="table especialidades-table align-middle mb-0" data-page-length="10">
                    <thead>
                        <tr>
                            <th scope="col" class="especialidades-column-id">ID</th>
                            <th scope="col">Especialidad</th>
                            <th scope="col">Descripción</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($lista as $item)
                        <tr>
                            <td class="especialidades-id">#{{ $item->id }}</td>
                            <td>
                              <div class="especialidades-identity">
                                <div class="especialidades-avatar"><span class="material-symbols-outlined">workspace_premium</span></div>
                                <div class="especialidades-identity-data">
                                  <span class="especialidades-fullname" data-filtro-valor>{{ $item->especialidad }}</span>
                                </div>
                              </div>
                            </td>
                            <td class="especialidades-muted" title="{{ $item->descripcion }}">{{ \Illuminate\Support\Str::limit((string) ($item->descripcion ?? ""), 70) ?: "—" }}</td>
                            <td>
                                <div class="especialidades-actions">
                                    <button type="button" class="especialidades-action-button"
                                            data-bs-toggle="modal" data-bs-target="#modalVer"
                                            data-id="{{ $item->id }}"
                                            title="Consultar especialidad" aria-label="Consultar especialidad">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </button>
                                    @if($puedeEditar)
                                        <button type="button" class="especialidades-action-button"
                                                data-bs-toggle="modal" data-bs-target="#modalEditar"
                                                data-id="{{ $item->id }}"
                                                title="Editar especialidad" aria-label="Editar especialidad">
                                            <span class="material-symbols-outlined">edit</span>
                                        </button>
                                    @endif
                                    @if($puedeEliminar)
                                        <button type="button" class="especialidades-action-button especialidades-action-delete"
                                                data-bs-toggle="modal" data-bs-target="#modalEliminar"
                                                data-id="{{ $item->id }}"
                                                data-nombre="{{ $item->especialidad }}"
                                                data-sub="{{ $item->descripcion ?? "" }}"
                                                title="Eliminar especialidad" aria-label="Eliminar especialidad">
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
    <div class="modal fade especialidades-modal" id="modalNuevo" tabindex="-1" aria-labelledby="tituloNuevo" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="especialidades-modal-header">
                    <div class="especialidades-modal-heading">
                        <div class="especialidades-modal-icon"><span class="material-symbols-outlined">add_circle</span></div>
                        <div>
                            <h2 class="especialidades-modal-title" id="tituloNuevo">Registrar especialidad</h2>
                            <p class="especialidades-modal-subtitle">Completa la información solicitada.</p>
                        </div>
                    </div>
                    <button type="button" class="especialidades-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="formNuevo" method="POST" action="{{ route('especialidades.insertar') }}" novalidate>
                    @csrf
                    <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                    <div class="modal-body especialidades-modal-body">
                        <div class="especialidades-form-section">
                          <div class="especialidades-form-section-heading">
                            <span class="material-symbols-outlined">workspace_premium</span>
                            <div>
                              <h3>Información del registro</h3>
                              <p>Datos que identifican este elemento del catálogo.</p>
                            </div>
                          </div>
                          <div class="row g-3">
                            <div class="col-md-12">
                              <div class="form-floating">
                                <input type="text" name="especialidad" id="nuevo_especialidad" class="form-control" placeholder="Especialidad" maxlength="50" required>
                                <label for="nuevo_especialidad">Especialidad</label>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="form-floating">
                                <textarea name="descripcion" id="nuevo_descripcion" class="form-control" placeholder="Descripción" maxlength="250"></textarea>
                                <label for="nuevo_descripcion">Descripción</label>
                              </div>
                              <div class="especialidades-field-hint">Máximo 250 caracteres.</div>
                            </div>
                          </div>
                        </div>
                    </div>

                    <div class="especialidades-modal-footer">
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
    <div class="modal fade especialidades-modal" id="modalEditar" tabindex="-1" aria-labelledby="tituloEditar" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="especialidades-modal-header">
                    <div class="especialidades-modal-heading">
                        <div class="especialidades-modal-icon"><span class="material-symbols-outlined">edit</span></div>
                        <div>
                            <h2 class="especialidades-modal-title" id="tituloEditar">Editar especialidad #<span id="editarTituloId">—</span></h2>
                            <p class="especialidades-modal-subtitle">Actualiza la información y guarda los cambios.</p>
                        </div>
                    </div>
                    <button type="button" class="especialidades-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="formEditar" method="POST"
                      data-url="{{ route('especialidades.actualizar', ['especialidad' => '__ID__']) }}"
                      data-buscar-url="{{ route('especialidades.buscar', ['id' => '__ID__']) }}"
                      novalidate>
                    @csrf
                    @method('PUT')
                    <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                    <div class="modal-body especialidades-modal-body">
                        <div class="especialidades-form-section">
                          <div class="especialidades-form-section-heading">
                            <span class="material-symbols-outlined">workspace_premium</span>
                            <div>
                              <h3>Información del registro</h3>
                              <p>Datos que identifican este elemento del catálogo.</p>
                            </div>
                          </div>
                          <div class="row g-3">
                            <div class="col-md-12">
                              <div class="form-floating">
                                <input type="text" name="especialidad" id="editar_especialidad" class="form-control" placeholder="Especialidad" maxlength="50" required>
                                <label for="editar_especialidad">Especialidad</label>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="form-floating">
                                <textarea name="descripcion" id="editar_descripcion" class="form-control" placeholder="Descripción" maxlength="250"></textarea>
                                <label for="editar_descripcion">Descripción</label>
                              </div>
                              <div class="especialidades-field-hint">Máximo 250 caracteres.</div>
                            </div>
                          </div>
                        </div>
                    </div>

                    <div class="especialidades-modal-footer">
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
    <div class="modal fade especialidades-modal" id="modalVer" tabindex="-1" aria-labelledby="tituloVer" aria-hidden="true"
         data-buscar-url="{{ route('especialidades.buscar', ['id' => '__ID__']) }}">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="especialidades-modal-header">
                    <div class="especialidades-modal-heading">
                        <div class="especialidades-modal-icon"><span class="material-symbols-outlined">visibility</span></div>
                        <div>
                            <h2 class="especialidades-modal-title" id="tituloVer">Detalle de especialidad</h2>
                            <p class="especialidades-modal-subtitle">Información completa del registro.</p>
                        </div>
                    </div>
                    <button type="button" class="especialidades-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                <div class="modal-body especialidades-modal-body">
                    <dl class="especialidades-detail-grid">
                        <div class="especialidades-detail-item"><dt>Identificador</dt><dd data-ver="id">—</dd></div>
                        <div class="especialidades-detail-item"><dt>Especialidad</dt><dd data-ver="especialidad">—</dd></div>
                        <div class="especialidades-detail-item is-wide"><dt>Descripción</dt><dd data-ver="descripcion">—</dd></div>
                    </dl>
                </div>

                <div class="especialidades-modal-footer">
                    <button type="button" class="btn btn-tonal" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: ELIMINAR --}}
    @if($puedeEliminar)
    <div class="modal fade especialidades-modal especialidades-delete-modal" id="modalEliminar" tabindex="-1" aria-labelledby="tituloEliminar" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="especialidades-delete-content">
                    <div class="especialidades-delete-icon"><span class="material-symbols-outlined">delete</span></div>

                    <button type="button" class="especialidades-modal-close especialidades-delete-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>

                    <div class="especialidades-delete-heading">
                        <span class="especialidades-delete-overline">Confirmación requerida</span>
                        <h2 id="tituloEliminar">¿Eliminar esta especialidad?</h2>
                        <p>Esta acción no se puede deshacer desde la interfaz.</p>
                    </div>

                    <form id="formEliminar" method="POST"
                          data-url="{{ route('especialidades.eliminar', ['especialidad' => '__ID__']) }}" novalidate>
                        @csrf
                        @method('DELETE')
                        <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                        <div class="especialidades-delete-identity">
                            <div class="especialidades-delete-avatar"><span class="material-symbols-outlined">workspace_premium</span></div>
                            <div class="especialidades-delete-data">
                                <span class="especialidades-delete-name" id="eliminar_nombre">—</span>
                                <span class="especialidades-delete-sub" id="eliminar_sub">—</span>
                                <span class="especialidades-delete-id">Identificador: #<span id="eliminar_id">—</span></span>
                            </div>
                        </div>

                        <div class="especialidades-delete-notice">
                            <span class="material-symbols-outlined">info</span>
                            <p>Se quitará la especialidad del catálogo. Revisa que no esté asignada a doctores.</p>
                        </div>

                        <div class="especialidades-delete-actions">
                            <button type="button" class="btn btn-tonal" data-bs-dismiss="modal">Conservar</button>
                            <button type="submit" class="btn especialidades-delete-confirm">
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
        VISTA: Catálogo de especialidades médicas.

        OBJETIVO:
        Administrar las especialidades profesionales que pueden
        asociarse a los doctores.

        CONTENIDO:
        - Encabezado.
        - Botón para agregar especialidad.
        - Tabla de especialidades.
        - Columnas: identificador, nombre y descripción.
        - Búsqueda por nombre.
        - Modal de creación.
        - Modal de edición.
        - Confirmación de eliminación.

        INTERACCIÓN:
        - Crear, consultar, actualizar y eliminar especialidades.
        - Mostrar validaciones y notificaciones.

        CONSIDERACIONES:
        - Los doctores se relacionan con este catálogo.
        - Evitar eliminar especialidades que estén siendo utilizadas.
        - Los tres roles pueden consultar el catálogo.
        - La administración corresponde al administrador.
    --}}
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('js/especialidad/especialidades.js') }}"></script>
@endpush
