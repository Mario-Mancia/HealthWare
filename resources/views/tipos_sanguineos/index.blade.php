{{-- HEALTHWARE-GENERADO: generado por generar-frontend.sh v1.0.0 · no mezclar con otros marcadores --}}
@extends('layouts.app_layout')

@section('titulo', 'Tipos Sanguineos')

@push('estilos')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('css/tipos_sanguineos.css') }}">
@endpush

@section('contenido')

    @php
        $rolActual = \Illuminate\Support\Str::of(auth()->user()?->rol?->rol ?? '')->ascii()->lower()->toString();
        $puedeCrear = in_array($rolActual, ['administrador']);
        $puedeEditar = in_array($rolActual, ['administrador']);
        $puedeEliminar = in_array($rolActual, ['administrador']);
        $lista = $tiposSanguineos ?? collect();
        $fmtFecha = fn($v, $f = 'd/m/Y') => $v ? \Illuminate\Support\Carbon::parse($v)->format($f) : '—';
    @endphp

    <div class="tipos-sanguineos-page">

        {{-- ENCABEZADO --}}
        <header class="tipos-sanguineos-header">
            <div class="tipos-sanguineos-heading">
                <div class="tipos-sanguineos-heading-icon">
                    <span class="material-symbols-outlined">bloodtype</span>
                </div>
                <div>
                    <h1 class="tipos-sanguineos-title">Tipos sanguíneos</h1>
                    <p class="tipos-sanguineos-subtitle">Catálogo usado en el formulario de pacientes.</p>
                </div>
            </div>

            @if($puedeCrear)
                <button type="button"
                        class="btn btn-primary tipos-sanguineos-create-button"
                        data-bs-toggle="modal"
                        data-bs-target="#modalNuevo">
                    <span class="material-symbols-outlined">add_circle</span>
                    Nuevo tipo sanguíneo
                </button>
            @endif
        </header>


        {{-- SUPERFICIE --}}
        <section class="tipos-sanguineos-surface">

            <div class="tipos-sanguineos-surface-header">
                <div>
                    <h2 class="tipos-sanguineos-surface-title">Tipos sanguíneos registrados</h2>
                    <p class="tipos-sanguineos-surface-description">Consulta y administra los tipos sanguíneos.</p>
                </div>
                <div class="tipos-sanguineos-record-indicator">
                    <span class="material-symbols-outlined">database</span>
                    <span>{{ $lista->count() }} registros</span>
                </div>
            </div>

            @if($lista->isEmpty())
                <div class="tipos-sanguineos-empty">
                    <span class="material-symbols-outlined">bloodtype</span>
                    <h3>Aún no hay registros</h3>
                    <p>Cuando existan datos aparecerán aquí.</p>
                </div>
            @else
            <div class="table-responsive tipos-sanguineos-table-container">
                <table id="tabla-tipos-sanguineos" class="table tipos-sanguineos-table align-middle mb-0" data-page-length="10">
                    <thead>
                        <tr>
                            <th scope="col" class="tipos-sanguineos-column-id">ID</th>
                            <th scope="col">Tipo sanguíneo</th>
                            <th scope="col">Descripción</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($lista as $item)
                        <tr>
                            <td class="tipos-sanguineos-id">#{{ $item->id }}</td>
                            <td>
                              <div class="tipos-sanguineos-identity">
                                <div class="tipos-sanguineos-avatar"><span class="material-symbols-outlined">bloodtype</span></div>
                                <div class="tipos-sanguineos-identity-data">
                                  <span class="tipos-sanguineos-fullname" data-filtro-valor>{{ $item->tipo_sanguineo }}</span>
                                </div>
                              </div>
                            </td>
                            <td class="tipos-sanguineos-muted" title="{{ $item->descripcion }}">{{ \Illuminate\Support\Str::limit((string) ($item->descripcion ?? ""), 70) ?: "—" }}</td>
                            <td>
                                <div class="tipos-sanguineos-actions">
                                    <button type="button" class="tipos-sanguineos-action-button"
                                            data-bs-toggle="modal" data-bs-target="#modalVer"
                                            data-id="{{ $item->id }}"
                                            title="Consultar tipo sanguíneo" aria-label="Consultar tipo sanguíneo">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </button>
                                    @if($puedeEditar)
                                        <button type="button" class="tipos-sanguineos-action-button"
                                                data-bs-toggle="modal" data-bs-target="#modalEditar"
                                                data-id="{{ $item->id }}"
                                                title="Editar tipo sanguíneo" aria-label="Editar tipo sanguíneo">
                                            <span class="material-symbols-outlined">edit</span>
                                        </button>
                                    @endif
                                    @if($puedeEliminar)
                                        <button type="button" class="tipos-sanguineos-action-button tipos-sanguineos-action-delete"
                                                data-bs-toggle="modal" data-bs-target="#modalEliminar"
                                                data-id="{{ $item->id }}"
                                                data-nombre="{{ $item->tipo_sanguineo }}"
                                                data-sub="{{ $item->descripcion ?? "" }}"
                                                title="Eliminar tipo sanguíneo" aria-label="Eliminar tipo sanguíneo">
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
    <div class="modal fade tipos-sanguineos-modal" id="modalNuevo" tabindex="-1" aria-labelledby="tituloNuevo" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="tipos-sanguineos-modal-header">
                    <div class="tipos-sanguineos-modal-heading">
                        <div class="tipos-sanguineos-modal-icon"><span class="material-symbols-outlined">add_circle</span></div>
                        <div>
                            <h2 class="tipos-sanguineos-modal-title" id="tituloNuevo">Registrar tipo sanguíneo</h2>
                            <p class="tipos-sanguineos-modal-subtitle">Completa la información solicitada.</p>
                        </div>
                    </div>
                    <button type="button" class="tipos-sanguineos-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="formNuevo" method="POST" action="{{ route('tipos-sanguineos.insertar') }}" novalidate>
                    @csrf
                    <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                    <div class="modal-body tipos-sanguineos-modal-body">
                        <div class="tipos-sanguineos-form-section">
                          <div class="tipos-sanguineos-form-section-heading">
                            <span class="material-symbols-outlined">bloodtype</span>
                            <div>
                              <h3>Información del registro</h3>
                              <p>Datos que identifican este elemento del catálogo.</p>
                            </div>
                          </div>
                          <div class="row g-3">
                            <div class="col-md-12">
                              <div class="form-floating">
                                <input type="text" name="tipo_sanguineo" id="nuevo_tipo_sanguineo" class="form-control" placeholder="Tipo sanguíneo" maxlength="50" required>
                                <label for="nuevo_tipo_sanguineo">Tipo sanguíneo</label>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="form-floating">
                                <textarea name="descripcion" id="nuevo_descripcion" class="form-control" placeholder="Descripción" maxlength="250"></textarea>
                                <label for="nuevo_descripcion">Descripción</label>
                              </div>
                              <div class="tipos-sanguineos-field-hint">Máximo 250 caracteres.</div>
                            </div>
                          </div>
                        </div>
                    </div>

                    <div class="tipos-sanguineos-modal-footer">
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
    <div class="modal fade tipos-sanguineos-modal" id="modalEditar" tabindex="-1" aria-labelledby="tituloEditar" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="tipos-sanguineos-modal-header">
                    <div class="tipos-sanguineos-modal-heading">
                        <div class="tipos-sanguineos-modal-icon"><span class="material-symbols-outlined">edit</span></div>
                        <div>
                            <h2 class="tipos-sanguineos-modal-title" id="tituloEditar">Editar tipo sanguíneo #<span id="editarTituloId">—</span></h2>
                            <p class="tipos-sanguineos-modal-subtitle">Actualiza la información y guarda los cambios.</p>
                        </div>
                    </div>
                    <button type="button" class="tipos-sanguineos-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="formEditar" method="POST"
                      data-url="{{ route('tipos-sanguineos.actualizar', ['tipoSanguineo' => '__ID__']) }}"
                      data-buscar-url="{{ route('tipos-sanguineos.buscar', ['id' => '__ID__']) }}"
                      novalidate>
                    @csrf
                    @method('PUT')
                    <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                    <div class="modal-body tipos-sanguineos-modal-body">
                        <div class="tipos-sanguineos-form-section">
                          <div class="tipos-sanguineos-form-section-heading">
                            <span class="material-symbols-outlined">bloodtype</span>
                            <div>
                              <h3>Información del registro</h3>
                              <p>Datos que identifican este elemento del catálogo.</p>
                            </div>
                          </div>
                          <div class="row g-3">
                            <div class="col-md-12">
                              <div class="form-floating">
                                <input type="text" name="tipo_sanguineo" id="editar_tipo_sanguineo" class="form-control" placeholder="Tipo sanguíneo" maxlength="50" required>
                                <label for="editar_tipo_sanguineo">Tipo sanguíneo</label>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="form-floating">
                                <textarea name="descripcion" id="editar_descripcion" class="form-control" placeholder="Descripción" maxlength="250"></textarea>
                                <label for="editar_descripcion">Descripción</label>
                              </div>
                              <div class="tipos-sanguineos-field-hint">Máximo 250 caracteres.</div>
                            </div>
                          </div>
                        </div>
                    </div>

                    <div class="tipos-sanguineos-modal-footer">
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
    <div class="modal fade tipos-sanguineos-modal" id="modalVer" tabindex="-1" aria-labelledby="tituloVer" aria-hidden="true"
         data-buscar-url="{{ route('tipos-sanguineos.buscar', ['id' => '__ID__']) }}">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="tipos-sanguineos-modal-header">
                    <div class="tipos-sanguineos-modal-heading">
                        <div class="tipos-sanguineos-modal-icon"><span class="material-symbols-outlined">visibility</span></div>
                        <div>
                            <h2 class="tipos-sanguineos-modal-title" id="tituloVer">Detalle de tipo sanguíneo</h2>
                            <p class="tipos-sanguineos-modal-subtitle">Información completa del registro.</p>
                        </div>
                    </div>
                    <button type="button" class="tipos-sanguineos-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                <div class="modal-body tipos-sanguineos-modal-body">
                    <dl class="tipos-sanguineos-detail-grid">
                        <div class="tipos-sanguineos-detail-item"><dt>Identificador</dt><dd data-ver="id">—</dd></div>
                        <div class="tipos-sanguineos-detail-item"><dt>Tipo sanguíneo</dt><dd data-ver="tipo_sanguineo">—</dd></div>
                        <div class="tipos-sanguineos-detail-item is-wide"><dt>Descripción</dt><dd data-ver="descripcion">—</dd></div>
                    </dl>
                </div>

                <div class="tipos-sanguineos-modal-footer">
                    <button type="button" class="btn btn-tonal" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: ELIMINAR --}}
    @if($puedeEliminar)
    <div class="modal fade tipos-sanguineos-modal tipos-sanguineos-delete-modal" id="modalEliminar" tabindex="-1" aria-labelledby="tituloEliminar" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="tipos-sanguineos-delete-content">
                    <div class="tipos-sanguineos-delete-icon"><span class="material-symbols-outlined">delete</span></div>

                    <button type="button" class="tipos-sanguineos-modal-close tipos-sanguineos-delete-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>

                    <div class="tipos-sanguineos-delete-heading">
                        <span class="tipos-sanguineos-delete-overline">Confirmación requerida</span>
                        <h2 id="tituloEliminar">¿Eliminar este tipo sanguíneo?</h2>
                        <p>Esta acción no se puede deshacer desde la interfaz.</p>
                    </div>

                    <form id="formEliminar" method="POST"
                          data-url="{{ route('tipos-sanguineos.eliminar', ['tipoSanguineo' => '__ID__']) }}" novalidate>
                        @csrf
                        @method('DELETE')
                        <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                        <div class="tipos-sanguineos-delete-identity">
                            <div class="tipos-sanguineos-delete-avatar"><span class="material-symbols-outlined">bloodtype</span></div>
                            <div class="tipos-sanguineos-delete-data">
                                <span class="tipos-sanguineos-delete-name" id="eliminar_nombre">—</span>
                                <span class="tipos-sanguineos-delete-sub" id="eliminar_sub">—</span>
                                <span class="tipos-sanguineos-delete-id">Identificador: #<span id="eliminar_id">—</span></span>
                            </div>
                        </div>

                        <div class="tipos-sanguineos-delete-notice">
                            <span class="material-symbols-outlined">info</span>
                            <p>No elimines tipos sanguíneos que estén asignados a pacientes.</p>
                        </div>

                        <div class="tipos-sanguineos-delete-actions">
                            <button type="button" class="btn btn-tonal" data-bs-dismiss="modal">Conservar</button>
                            <button type="submit" class="btn tipos-sanguineos-delete-confirm">
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
        VISTA: Catálogo de tipos sanguíneos.

        OBJETIVO:
        Consultar los tipos sanguíneos disponibles para asignarlos
        a los pacientes.

        CONTENIDO:
        - Encabezado y descripción del catálogo.
        - Botón para agregar un tipo sanguíneo, si el usuario tiene permisos.
        - Tabla con identificador y denominación del tipo sanguíneo.
        - Búsqueda por denominación.
        - Modal de creación.
        - Modal de edición.
        - Confirmación de eliminación.

        INTERACCIÓN:
        - Crear, consultar, actualizar y eliminar registros.
        - Mostrar errores de validación.
        - Actualizar la tabla después de cada operación.

        CONSIDERACIONES:
        - El catálogo es utilizado por el formulario de pacientes.
        - No eliminar registros que estén siendo utilizados por pacientes.
        - La consulta está permitida para los tres roles.
        - La administración corresponde al administrador.
    --}}
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('js/tipo_sanguineo/tipos_sanguineos.js') }}"></script>
@endpush
