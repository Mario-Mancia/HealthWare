{{-- HEALTHWARE-GENERADO: generado por generar-frontend.sh v1.0.0 · no mezclar con otros marcadores --}}
@extends('layouts.app_layout')

@section('titulo', 'Diagnósticos')

@push('estilos')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('css/diagnosticos.css') }}">
@endpush

@section('contenido')

    @php
        $rolActual = \Illuminate\Support\Str::of(auth()->user()?->rol?->rol ?? '')->ascii()->lower()->toString();
        $puedeCrear = in_array($rolActual, ['administrador']);
        $puedeEditar = in_array($rolActual, ['administrador']);
        $puedeEliminar = in_array($rolActual, ['administrador']);
        $lista = $diagnosticos ?? collect();
        $fmtFecha = fn($v, $f = 'd/m/Y') => $v ? \Illuminate\Support\Carbon::parse($v)->format($f) : '—';
    @endphp

    <div class="diagnosticos-page">

        {{-- ENCABEZADO --}}
        <header class="diagnosticos-header">
            <div class="diagnosticos-heading">
                <div class="diagnosticos-heading-icon">
                    <span class="material-symbols-outlined">stethoscope</span>
                </div>
                <div>
                    <h1 class="diagnosticos-title">Catálogo de diagnósticos</h1>
                    <p class="diagnosticos-subtitle">Diagnósticos disponibles para los registros médicos.</p>
                </div>
            </div>

            @if($puedeCrear)
                <button type="button"
                        class="btn btn-primary diagnosticos-create-button"
                        data-bs-toggle="modal"
                        data-bs-target="#modalNuevo">
                    <span class="material-symbols-outlined">add_circle</span>
                    Nuevo diagnóstico
                </button>
            @endif
        </header>


        {{-- SUPERFICIE --}}
        <section class="diagnosticos-surface">

            <div class="diagnosticos-surface-header">
                <div>
                    <h2 class="diagnosticos-surface-title">Diagnósticos registrados</h2>
                    <p class="diagnosticos-surface-description">Consulta y administra el catálogo de diagnósticos.</p>
                </div>
                <div class="diagnosticos-record-indicator">
                    <span class="material-symbols-outlined">database</span>
                    <span>{{ $lista->count() }} registros</span>
                </div>
            </div>

            @if($lista->isEmpty())
                <div class="diagnosticos-empty">
                    <span class="material-symbols-outlined">stethoscope</span>
                    <h3>Aún no hay registros</h3>
                    <p>Cuando existan datos aparecerán aquí.</p>
                </div>
            @else
            <div class="table-responsive diagnosticos-table-container">
                <table id="tabla-diagnosticos" class="table diagnosticos-table align-middle mb-0" data-page-length="10">
                    <thead>
                        <tr>
                            <th scope="col" class="diagnosticos-column-id">ID</th>
                            <th scope="col">Diagnóstico</th>
                            <th scope="col">Descripción</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($lista as $item)
                        <tr>
                            <td class="diagnosticos-id">#{{ $item->id }}</td>
                            <td>
                              <div class="diagnosticos-identity">
                                <div class="diagnosticos-avatar"><span class="material-symbols-outlined">stethoscope</span></div>
                                <div class="diagnosticos-identity-data">
                                  <span class="diagnosticos-fullname" data-filtro-valor>{{ $item->diagnostico }}</span>
                                </div>
                              </div>
                            </td>
                            <td class="diagnosticos-muted" title="{{ $item->descripcion }}">{{ \Illuminate\Support\Str::limit((string) ($item->descripcion ?? ""), 70) ?: "—" }}</td>
                            <td>
                                <div class="diagnosticos-actions">
                                    <button type="button" class="diagnosticos-action-button"
                                            data-bs-toggle="modal" data-bs-target="#modalVer"
                                            data-id="{{ $item->id }}"
                                            title="Consultar diagnóstico" aria-label="Consultar diagnóstico">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </button>
                                    @if($puedeEditar)
                                        <button type="button" class="diagnosticos-action-button"
                                                data-bs-toggle="modal" data-bs-target="#modalEditar"
                                                data-id="{{ $item->id }}"
                                                title="Editar diagnóstico" aria-label="Editar diagnóstico">
                                            <span class="material-symbols-outlined">edit</span>
                                        </button>
                                    @endif
                                    @if($puedeEliminar)
                                        <button type="button" class="diagnosticos-action-button diagnosticos-action-delete"
                                                data-bs-toggle="modal" data-bs-target="#modalEliminar"
                                                data-id="{{ $item->id }}"
                                                data-nombre="{{ $item->diagnostico }}"
                                                data-sub="{{ $item->descripcion ?? "" }}"
                                                title="Eliminar diagnóstico" aria-label="Eliminar diagnóstico">
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
    <div class="modal fade diagnosticos-modal" id="modalNuevo" tabindex="-1" aria-labelledby="tituloNuevo" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="diagnosticos-modal-header">
                    <div class="diagnosticos-modal-heading">
                        <div class="diagnosticos-modal-icon"><span class="material-symbols-outlined">add_circle</span></div>
                        <div>
                            <h2 class="diagnosticos-modal-title" id="tituloNuevo">Registrar diagnóstico</h2>
                            <p class="diagnosticos-modal-subtitle">Completa la información solicitada.</p>
                        </div>
                    </div>
                    <button type="button" class="diagnosticos-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="formNuevo" method="POST" action="{{ route('diagnosticos.insertar') }}" novalidate>
                    @csrf
                    <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                    <div class="modal-body diagnosticos-modal-body">
                        <div class="diagnosticos-form-section">
                          <div class="diagnosticos-form-section-heading">
                            <span class="material-symbols-outlined">stethoscope</span>
                            <div>
                              <h3>Información del registro</h3>
                              <p>Datos que identifican este elemento del catálogo.</p>
                            </div>
                          </div>
                          <div class="row g-3">
                            <div class="col-md-12">
                              <div class="form-floating">
                                <input type="text" name="diagnostico" id="nuevo_diagnostico" class="form-control" placeholder="Diagnóstico" maxlength="50" required>
                                <label for="nuevo_diagnostico">Diagnóstico</label>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="form-floating">
                                <textarea name="descripcion" id="nuevo_descripcion" class="form-control" placeholder="Descripción" maxlength="250"></textarea>
                                <label for="nuevo_descripcion">Descripción</label>
                              </div>
                              <div class="diagnosticos-field-hint">Máximo 250 caracteres.</div>
                            </div>
                          </div>
                        </div>
                    </div>

                    <div class="diagnosticos-modal-footer">
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
    <div class="modal fade diagnosticos-modal" id="modalEditar" tabindex="-1" aria-labelledby="tituloEditar" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="diagnosticos-modal-header">
                    <div class="diagnosticos-modal-heading">
                        <div class="diagnosticos-modal-icon"><span class="material-symbols-outlined">edit</span></div>
                        <div>
                            <h2 class="diagnosticos-modal-title" id="tituloEditar">Editar diagnóstico #<span id="editarTituloId">—</span></h2>
                            <p class="diagnosticos-modal-subtitle">Actualiza la información y guarda los cambios.</p>
                        </div>
                    </div>
                    <button type="button" class="diagnosticos-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="formEditar" method="POST"
                      data-url="{{ route('diagnosticos.actualizar', ['diagnostico' => '__ID__']) }}"
                      data-buscar-url="{{ route('diagnosticos.buscar', ['id' => '__ID__']) }}"
                      novalidate>
                    @csrf
                    @method('PUT')
                    <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                    <div class="modal-body diagnosticos-modal-body">
                        <div class="diagnosticos-form-section">
                          <div class="diagnosticos-form-section-heading">
                            <span class="material-symbols-outlined">stethoscope</span>
                            <div>
                              <h3>Información del registro</h3>
                              <p>Datos que identifican este elemento del catálogo.</p>
                            </div>
                          </div>
                          <div class="row g-3">
                            <div class="col-md-12">
                              <div class="form-floating">
                                <input type="text" name="diagnostico" id="editar_diagnostico" class="form-control" placeholder="Diagnóstico" maxlength="50" required>
                                <label for="editar_diagnostico">Diagnóstico</label>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="form-floating">
                                <textarea name="descripcion" id="editar_descripcion" class="form-control" placeholder="Descripción" maxlength="250"></textarea>
                                <label for="editar_descripcion">Descripción</label>
                              </div>
                              <div class="diagnosticos-field-hint">Máximo 250 caracteres.</div>
                            </div>
                          </div>
                        </div>
                    </div>

                    <div class="diagnosticos-modal-footer">
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
    <div class="modal fade diagnosticos-modal" id="modalVer" tabindex="-1" aria-labelledby="tituloVer" aria-hidden="true"
         data-buscar-url="{{ route('diagnosticos.buscar', ['id' => '__ID__']) }}">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="diagnosticos-modal-header">
                    <div class="diagnosticos-modal-heading">
                        <div class="diagnosticos-modal-icon"><span class="material-symbols-outlined">visibility</span></div>
                        <div>
                            <h2 class="diagnosticos-modal-title" id="tituloVer">Detalle de diagnóstico</h2>
                            <p class="diagnosticos-modal-subtitle">Información completa del registro.</p>
                        </div>
                    </div>
                    <button type="button" class="diagnosticos-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                <div class="modal-body diagnosticos-modal-body">
                    <dl class="diagnosticos-detail-grid">
                        <div class="diagnosticos-detail-item"><dt>Identificador</dt><dd data-ver="id">—</dd></div>
                        <div class="diagnosticos-detail-item"><dt>Diagnóstico</dt><dd data-ver="diagnostico">—</dd></div>
                        <div class="diagnosticos-detail-item is-wide"><dt>Descripción</dt><dd data-ver="descripcion">—</dd></div>
                    </dl>
                </div>

                <div class="diagnosticos-modal-footer">
                    <button type="button" class="btn btn-tonal" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: ELIMINAR --}}
    @if($puedeEliminar)
    <div class="modal fade diagnosticos-modal diagnosticos-delete-modal" id="modalEliminar" tabindex="-1" aria-labelledby="tituloEliminar" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="diagnosticos-delete-content">
                    <div class="diagnosticos-delete-icon"><span class="material-symbols-outlined">delete</span></div>

                    <button type="button" class="diagnosticos-modal-close diagnosticos-delete-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>

                    <div class="diagnosticos-delete-heading">
                        <span class="diagnosticos-delete-overline">Confirmación requerida</span>
                        <h2 id="tituloEliminar">¿Eliminar este diagnóstico?</h2>
                        <p>Esta acción no se puede deshacer desde la interfaz.</p>
                    </div>

                    <form id="formEliminar" method="POST"
                          data-url="{{ route('diagnosticos.eliminar', ['diagnostico' => '__ID__']) }}" novalidate>
                        @csrf
                        @method('DELETE')
                        <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                        <div class="diagnosticos-delete-identity">
                            <div class="diagnosticos-delete-avatar"><span class="material-symbols-outlined">stethoscope</span></div>
                            <div class="diagnosticos-delete-data">
                                <span class="diagnosticos-delete-name" id="eliminar_nombre">—</span>
                                <span class="diagnosticos-delete-sub" id="eliminar_sub">—</span>
                                <span class="diagnosticos-delete-id">Identificador: #<span id="eliminar_id">—</span></span>
                            </div>
                        </div>

                        <div class="diagnosticos-delete-notice">
                            <span class="material-symbols-outlined">info</span>
                            <p>Se quitará el diagnóstico del catálogo. Revisa que no esté asociado a registros médicos.</p>
                        </div>

                        <div class="diagnosticos-delete-actions">
                            <button type="button" class="btn btn-tonal" data-bs-dismiss="modal">Conservar</button>
                            <button type="submit" class="btn diagnosticos-delete-confirm">
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
        VISTA: Catálogo de diagnósticos médicos.

        OBJETIVO:
        Mantener un catálogo de diagnósticos que puedan asociarse
        a los registros médicos de los pacientes.

        CONTENIDO:
        - Encabezado y descripción.
        - Botón para registrar diagnóstico.
        - Tabla de diagnósticos.
        - Identificador.
        - Nombre o denominación.
        - Descripción.
        - Búsqueda por nombre.
        - Modal de creación.
        - Modal de edición.
        - Confirmación de eliminación.

        INTERACCIÓN:
        - Consultar diagnósticos.
        - Administrar el catálogo según permisos.
        - Mostrar errores y confirmaciones.

        CONSIDERACIONES:
        - Los diagnósticos se asocian a registros médicos mediante
          registro_medico_diagnosticos.
        - No confundir el catálogo con los diagnósticos particulares
          de un paciente.
        - La consulta está permitida para administrador y médico.
        - La modificación del catálogo corresponde al administrador.
    --}}
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('js/diagnostico/diagnosticos.js') }}"></script>
@endpush
