{{-- HEALTHWARE-GENERADO: generado por generar-frontend.sh v1.0.0 · no mezclar con otros marcadores --}}
@extends('layouts.app_layout')

@section('titulo', 'Condiciones Médicas')

@push('estilos')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('css/condiciones_medicas.css') }}">
@endpush

@section('contenido')

    @php
        $rolActual = \Illuminate\Support\Str::of(auth()->user()?->rol?->rol ?? '')->ascii()->lower()->toString();
        $puedeCrear = in_array($rolActual, ['administrador']);
        $puedeEditar = in_array($rolActual, ['administrador']);
        $puedeEliminar = in_array($rolActual, ['administrador']);
        $lista = $condicionesMedicas ?? collect();
        $fmtFecha = fn($v, $f = 'd/m/Y') => $v ? \Illuminate\Support\Carbon::parse($v)->format($f) : '—';
    @endphp

    <div class="condiciones-medicas-page">

        {{-- ENCABEZADO --}}
        <header class="condiciones-medicas-header">
            <div class="condiciones-medicas-heading">
                <div class="condiciones-medicas-heading-icon">
                    <span class="material-symbols-outlined">monitor_heart</span>
                </div>
                <div>
                    <h1 class="condiciones-medicas-title">Condiciones médicas</h1>
                    <p class="condiciones-medicas-subtitle">Catálogo de condiciones médicas de los pacientes.</p>
                </div>
            </div>

            @if($puedeCrear)
                <button type="button"
                        class="btn btn-primary condiciones-medicas-create-button"
                        data-bs-toggle="modal"
                        data-bs-target="#modalNuevo">
                    <span class="material-symbols-outlined">add_circle</span>
                    Nueva condición médica
                </button>
            @endif
        </header>


        {{-- SUPERFICIE --}}
        <section class="condiciones-medicas-surface">

            <div class="condiciones-medicas-surface-header">
                <div>
                    <h2 class="condiciones-medicas-surface-title">Condiciones registradas</h2>
                    <p class="condiciones-medicas-surface-description">Consulta y administra las condiciones médicas disponibles.</p>
                </div>
                <div class="condiciones-medicas-record-indicator">
                    <span class="material-symbols-outlined">database</span>
                    <span>{{ $lista->count() }} registros</span>
                </div>
            </div>

            @if($lista->isEmpty())
                <div class="condiciones-medicas-empty">
                    <span class="material-symbols-outlined">monitor_heart</span>
                    <h3>Aún no hay registros</h3>
                    <p>Cuando existan datos aparecerán aquí.</p>
                </div>
            @else
            <div class="table-responsive condiciones-medicas-table-container">
                <table id="tabla-condiciones-medicas" class="table condiciones-medicas-table align-middle mb-0" data-page-length="10">
                    <thead>
                        <tr>
                            <th scope="col" class="condiciones-medicas-column-id">ID</th>
                            <th scope="col">Condición médica</th>
                            <th scope="col">Descripción</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($lista as $item)
                        <tr>
                            <td class="condiciones-medicas-id">#{{ $item->id }}</td>
                            <td>
                              <div class="condiciones-medicas-identity">
                                <div class="condiciones-medicas-avatar"><span class="material-symbols-outlined">monitor_heart</span></div>
                                <div class="condiciones-medicas-identity-data">
                                  <span class="condiciones-medicas-fullname" data-filtro-valor>{{ $item->condicion_medica }}</span>
                                </div>
                              </div>
                            </td>
                            <td class="condiciones-medicas-muted" title="{{ $item->descripcion }}">{{ \Illuminate\Support\Str::limit((string) ($item->descripcion ?? ""), 70) ?: "—" }}</td>
                            <td>
                                <div class="condiciones-medicas-actions">
                                    <button type="button" class="condiciones-medicas-action-button"
                                            data-bs-toggle="modal" data-bs-target="#modalVer"
                                            data-id="{{ $item->id }}"
                                            title="Consultar condición médica" aria-label="Consultar condición médica">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </button>
                                    @if($puedeEditar)
                                        <button type="button" class="condiciones-medicas-action-button"
                                                data-bs-toggle="modal" data-bs-target="#modalEditar"
                                                data-id="{{ $item->id }}"
                                                title="Editar condición médica" aria-label="Editar condición médica">
                                            <span class="material-symbols-outlined">edit</span>
                                        </button>
                                    @endif
                                    @if($puedeEliminar)
                                        <button type="button" class="condiciones-medicas-action-button condiciones-medicas-action-delete"
                                                data-bs-toggle="modal" data-bs-target="#modalEliminar"
                                                data-id="{{ $item->id }}"
                                                data-nombre="{{ $item->condicion_medica }}"
                                                data-sub="{{ $item->descripcion ?? "" }}"
                                                title="Eliminar condición médica" aria-label="Eliminar condición médica">
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
    <div class="modal fade condiciones-medicas-modal" id="modalNuevo" tabindex="-1" aria-labelledby="tituloNuevo" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="condiciones-medicas-modal-header">
                    <div class="condiciones-medicas-modal-heading">
                        <div class="condiciones-medicas-modal-icon"><span class="material-symbols-outlined">add_circle</span></div>
                        <div>
                            <h2 class="condiciones-medicas-modal-title" id="tituloNuevo">Registrar condición médica</h2>
                            <p class="condiciones-medicas-modal-subtitle">Completa la información solicitada.</p>
                        </div>
                    </div>
                    <button type="button" class="condiciones-medicas-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="formNuevo" method="POST" action="{{ route('condiciones-medicas.insertar') }}" novalidate>
                    @csrf
                    <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                    <div class="modal-body condiciones-medicas-modal-body">
                        <div class="condiciones-medicas-form-section">
                          <div class="condiciones-medicas-form-section-heading">
                            <span class="material-symbols-outlined">monitor_heart</span>
                            <div>
                              <h3>Información del registro</h3>
                              <p>Datos que identifican este elemento del catálogo.</p>
                            </div>
                          </div>
                          <div class="row g-3">
                            <div class="col-md-12">
                              <div class="form-floating">
                                <input type="text" name="condicion_medica" id="nuevo_condicion_medica" class="form-control" placeholder="Condición médica" maxlength="50" required>
                                <label for="nuevo_condicion_medica">Condición médica</label>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="form-floating">
                                <textarea name="descripcion" id="nuevo_descripcion" class="form-control" placeholder="Descripción" maxlength="250"></textarea>
                                <label for="nuevo_descripcion">Descripción</label>
                              </div>
                              <div class="condiciones-medicas-field-hint">Máximo 250 caracteres.</div>
                            </div>
                          </div>
                        </div>
                    </div>

                    <div class="condiciones-medicas-modal-footer">
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
    <div class="modal fade condiciones-medicas-modal" id="modalEditar" tabindex="-1" aria-labelledby="tituloEditar" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="condiciones-medicas-modal-header">
                    <div class="condiciones-medicas-modal-heading">
                        <div class="condiciones-medicas-modal-icon"><span class="material-symbols-outlined">edit</span></div>
                        <div>
                            <h2 class="condiciones-medicas-modal-title" id="tituloEditar">Editar condición médica #<span id="editarTituloId">—</span></h2>
                            <p class="condiciones-medicas-modal-subtitle">Actualiza la información y guarda los cambios.</p>
                        </div>
                    </div>
                    <button type="button" class="condiciones-medicas-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="formEditar" method="POST"
                      data-url="{{ route('condiciones-medicas.actualizar', ['condicionMedica' => '__ID__']) }}"
                      data-buscar-url="{{ route('condiciones-medicas.buscar', ['id' => '__ID__']) }}"
                      novalidate>
                    @csrf
                    @method('PUT')
                    <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                    <div class="modal-body condiciones-medicas-modal-body">
                        <div class="condiciones-medicas-form-section">
                          <div class="condiciones-medicas-form-section-heading">
                            <span class="material-symbols-outlined">monitor_heart</span>
                            <div>
                              <h3>Información del registro</h3>
                              <p>Datos que identifican este elemento del catálogo.</p>
                            </div>
                          </div>
                          <div class="row g-3">
                            <div class="col-md-12">
                              <div class="form-floating">
                                <input type="text" name="condicion_medica" id="editar_condicion_medica" class="form-control" placeholder="Condición médica" maxlength="50" required>
                                <label for="editar_condicion_medica">Condición médica</label>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="form-floating">
                                <textarea name="descripcion" id="editar_descripcion" class="form-control" placeholder="Descripción" maxlength="250"></textarea>
                                <label for="editar_descripcion">Descripción</label>
                              </div>
                              <div class="condiciones-medicas-field-hint">Máximo 250 caracteres.</div>
                            </div>
                          </div>
                        </div>
                    </div>

                    <div class="condiciones-medicas-modal-footer">
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
    <div class="modal fade condiciones-medicas-modal" id="modalVer" tabindex="-1" aria-labelledby="tituloVer" aria-hidden="true"
         data-buscar-url="{{ route('condiciones-medicas.buscar', ['id' => '__ID__']) }}">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="condiciones-medicas-modal-header">
                    <div class="condiciones-medicas-modal-heading">
                        <div class="condiciones-medicas-modal-icon"><span class="material-symbols-outlined">visibility</span></div>
                        <div>
                            <h2 class="condiciones-medicas-modal-title" id="tituloVer">Detalle de condición médica</h2>
                            <p class="condiciones-medicas-modal-subtitle">Información completa del registro.</p>
                        </div>
                    </div>
                    <button type="button" class="condiciones-medicas-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                <div class="modal-body condiciones-medicas-modal-body">
                    <dl class="condiciones-medicas-detail-grid">
                        <div class="condiciones-medicas-detail-item"><dt>Identificador</dt><dd data-ver="id">—</dd></div>
                        <div class="condiciones-medicas-detail-item"><dt>Condición médica</dt><dd data-ver="condicion_medica">—</dd></div>
                        <div class="condiciones-medicas-detail-item is-wide"><dt>Descripción</dt><dd data-ver="descripcion">—</dd></div>
                    </dl>
                </div>

                <div class="condiciones-medicas-modal-footer">
                    <button type="button" class="btn btn-tonal" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: ELIMINAR --}}
    @if($puedeEliminar)
    <div class="modal fade condiciones-medicas-modal condiciones-medicas-delete-modal" id="modalEliminar" tabindex="-1" aria-labelledby="tituloEliminar" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="condiciones-medicas-delete-content">
                    <div class="condiciones-medicas-delete-icon"><span class="material-symbols-outlined">delete</span></div>

                    <button type="button" class="condiciones-medicas-modal-close condiciones-medicas-delete-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>

                    <div class="condiciones-medicas-delete-heading">
                        <span class="condiciones-medicas-delete-overline">Confirmación requerida</span>
                        <h2 id="tituloEliminar">¿Eliminar esta condición médica?</h2>
                        <p>Esta acción no se puede deshacer desde la interfaz.</p>
                    </div>

                    <form id="formEliminar" method="POST"
                          data-url="{{ route('condiciones-medicas.eliminar', ['condicionMedica' => '__ID__']) }}" novalidate>
                        @csrf
                        @method('DELETE')
                        <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                        <div class="condiciones-medicas-delete-identity">
                            <div class="condiciones-medicas-delete-avatar"><span class="material-symbols-outlined">monitor_heart</span></div>
                            <div class="condiciones-medicas-delete-data">
                                <span class="condiciones-medicas-delete-name" id="eliminar_nombre">—</span>
                                <span class="condiciones-medicas-delete-sub" id="eliminar_sub">—</span>
                                <span class="condiciones-medicas-delete-id">Identificador: #<span id="eliminar_id">—</span></span>
                            </div>
                        </div>

                        <div class="condiciones-medicas-delete-notice">
                            <span class="material-symbols-outlined">info</span>
                            <p>Se quitará la condición del catálogo. Revisa que no esté asociada a pacientes.</p>
                        </div>

                        <div class="condiciones-medicas-delete-actions">
                            <button type="button" class="btn btn-tonal" data-bs-dismiss="modal">Conservar</button>
                            <button type="submit" class="btn condiciones-medicas-delete-confirm">
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
        VISTA: Catálogo de condiciones médicas.

        OBJETIVO:
        Administrar las condiciones médicas que pueden asociarse
        a los pacientes.

        CONTENIDO:
        - Encabezado.
        - Botón para agregar condición médica.
        - Tabla con identificador, nombre y descripción.
        - Búsqueda por denominación.
        - Modal de creación.
        - Modal de edición.
        - Confirmación de eliminación.

        INTERACCIÓN:
        - Crear, consultar, actualizar y eliminar condiciones.
        - Mostrar mensajes de validación.

        CONSIDERACIONES:
        - Este catálogo se relaciona con pacientes mediante condiciones_pacientes.
        - No confundir una condición del catálogo con la condición
          que tiene registrada un paciente.
        - La asociación individual se administra desde el expediente clínico.
        - Consulta para administrador y médico.
        - Administración del catálogo exclusiva del administrador.
    --}}
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('js/condicion_medica/condiciones_medicas.js') }}"></script>
@endpush
