{{-- HEALTHWARE-GENERADO: generado por generar-frontend.sh v1.0.0 · no mezclar con otros marcadores --}}
@extends('layouts.app_layout')

@section('titulo', 'Servicios')

@push('estilos')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('css/servicios.css') }}">
@endpush

@section('contenido')

    @php
        $rolActual = \Illuminate\Support\Str::of(auth()->user()?->rol?->rol ?? '')->ascii()->lower()->toString();
        $puedeCrear = in_array($rolActual, ['administrador']);
        $puedeEditar = in_array($rolActual, ['administrador']);
        $puedeEliminar = in_array($rolActual, ['administrador']);
        $lista = $servicios ?? collect();
        $fmtFecha = fn($v, $f = 'd/m/Y') => $v ? \Illuminate\Support\Carbon::parse($v)->format($f) : '—';
    @endphp

    <div class="servicios-page">

        {{-- ENCABEZADO --}}
        <header class="servicios-header">
            <div class="servicios-heading">
                <div class="servicios-heading-icon">
                    <span class="material-symbols-outlined">medical_services</span>
                </div>
                <div>
                    <h1 class="servicios-title">Catálogo de servicios</h1>
                    <p class="servicios-subtitle">Servicios que ofrece la clínica y se usan en la facturación.</p>
                </div>
            </div>

            @if($puedeCrear)
                <button type="button"
                        class="btn btn-primary servicios-create-button"
                        data-bs-toggle="modal"
                        data-bs-target="#modalNuevo">
                    <span class="material-symbols-outlined">add_circle</span>
                    Nuevo servicio
                </button>
            @endif
        </header>


        {{-- SUPERFICIE --}}
        <section class="servicios-surface">

            <div class="servicios-surface-header">
                <div>
                    <h2 class="servicios-surface-title">Servicios registrados</h2>
                    <p class="servicios-surface-description">Consulta y administra los servicios disponibles.</p>
                </div>
                <div class="servicios-record-indicator">
                    <span class="material-symbols-outlined">database</span>
                    <span>{{ $lista->count() }} registros</span>
                </div>
            </div>

            @if($lista->isEmpty())
                <div class="servicios-empty">
                    <span class="material-symbols-outlined">medical_services</span>
                    <h3>Aún no hay registros</h3>
                    <p>Cuando existan datos aparecerán aquí.</p>
                </div>
            @else
            <div class="table-responsive servicios-table-container">
                <table id="tabla-servicios" class="table servicios-table align-middle mb-0" data-page-length="10">
                    <thead>
                        <tr>
                            <th scope="col" class="servicios-column-id">ID</th>
                            <th scope="col">Servicio</th>
                            <th scope="col">Descripción</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($lista as $item)
                        <tr>
                            <td class="servicios-id">#{{ $item->id }}</td>
                            <td>
                              <div class="servicios-identity">
                                <div class="servicios-avatar"><span class="material-symbols-outlined">medical_services</span></div>
                                <div class="servicios-identity-data">
                                  <span class="servicios-fullname" data-filtro-valor>{{ $item->servicio }}</span>
                                </div>
                              </div>
                            </td>
                            <td class="servicios-muted" title="{{ $item->descripcion }}">{{ \Illuminate\Support\Str::limit((string) ($item->descripcion ?? ""), 70) ?: "—" }}</td>
                            <td>
                                <div class="servicios-actions">
                                    <button type="button" class="servicios-action-button"
                                            data-bs-toggle="modal" data-bs-target="#modalVer"
                                            data-id="{{ $item->id }}"
                                            title="Consultar servicio" aria-label="Consultar servicio">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </button>
                                    @if($puedeEditar)
                                        <button type="button" class="servicios-action-button"
                                                data-bs-toggle="modal" data-bs-target="#modalEditar"
                                                data-id="{{ $item->id }}"
                                                title="Editar servicio" aria-label="Editar servicio">
                                            <span class="material-symbols-outlined">edit</span>
                                        </button>
                                    @endif
                                    @if($puedeEliminar)
                                        <button type="button" class="servicios-action-button servicios-action-delete"
                                                data-bs-toggle="modal" data-bs-target="#modalEliminar"
                                                data-id="{{ $item->id }}"
                                                data-nombre="{{ $item->servicio }}"
                                                data-sub="{{ $item->descripcion ?? "" }}"
                                                title="Eliminar servicio" aria-label="Eliminar servicio">
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
    <div class="modal fade servicios-modal" id="modalNuevo" tabindex="-1" aria-labelledby="tituloNuevo" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="servicios-modal-header">
                    <div class="servicios-modal-heading">
                        <div class="servicios-modal-icon"><span class="material-symbols-outlined">add_circle</span></div>
                        <div>
                            <h2 class="servicios-modal-title" id="tituloNuevo">Registrar servicio</h2>
                            <p class="servicios-modal-subtitle">Completa la información solicitada.</p>
                        </div>
                    </div>
                    <button type="button" class="servicios-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="formNuevo" method="POST" action="{{ route('servicios.insertar') }}" novalidate>
                    @csrf
                    <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                    <div class="modal-body servicios-modal-body">
                        <div class="servicios-form-section">
                          <div class="servicios-form-section-heading">
                            <span class="material-symbols-outlined">medical_services</span>
                            <div>
                              <h3>Información del registro</h3>
                              <p>Datos que identifican este elemento del catálogo.</p>
                            </div>
                          </div>
                          <div class="row g-3">
                            <div class="col-md-12">
                              <div class="form-floating">
                                <input type="text" name="servicio" id="nuevo_servicio" class="form-control" placeholder="Servicio" maxlength="50" required>
                                <label for="nuevo_servicio">Servicio</label>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="form-floating">
                                <textarea name="descripcion" id="nuevo_descripcion" class="form-control" placeholder="Descripción" maxlength="250"></textarea>
                                <label for="nuevo_descripcion">Descripción</label>
                              </div>
                              <div class="servicios-field-hint">Máximo 250 caracteres.</div>
                            </div>
                          </div>
                        </div>
                    </div>

                    <div class="servicios-modal-footer">
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
    <div class="modal fade servicios-modal" id="modalEditar" tabindex="-1" aria-labelledby="tituloEditar" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="servicios-modal-header">
                    <div class="servicios-modal-heading">
                        <div class="servicios-modal-icon"><span class="material-symbols-outlined">edit</span></div>
                        <div>
                            <h2 class="servicios-modal-title" id="tituloEditar">Editar servicio #<span id="editarTituloId">—</span></h2>
                            <p class="servicios-modal-subtitle">Actualiza la información y guarda los cambios.</p>
                        </div>
                    </div>
                    <button type="button" class="servicios-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="formEditar" method="POST"
                      data-url="{{ route('servicios.actualizar', ['servicio' => '__ID__']) }}"
                      data-buscar-url="{{ route('servicios.buscar', ['id' => '__ID__']) }}"
                      novalidate>
                    @csrf
                    @method('PUT')
                    <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                    <div class="modal-body servicios-modal-body">
                        <div class="servicios-form-section">
                          <div class="servicios-form-section-heading">
                            <span class="material-symbols-outlined">medical_services</span>
                            <div>
                              <h3>Información del registro</h3>
                              <p>Datos que identifican este elemento del catálogo.</p>
                            </div>
                          </div>
                          <div class="row g-3">
                            <div class="col-md-12">
                              <div class="form-floating">
                                <input type="text" name="servicio" id="editar_servicio" class="form-control" placeholder="Servicio" maxlength="50" required>
                                <label for="editar_servicio">Servicio</label>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="form-floating">
                                <textarea name="descripcion" id="editar_descripcion" class="form-control" placeholder="Descripción" maxlength="250"></textarea>
                                <label for="editar_descripcion">Descripción</label>
                              </div>
                              <div class="servicios-field-hint">Máximo 250 caracteres.</div>
                            </div>
                          </div>
                        </div>
                    </div>

                    <div class="servicios-modal-footer">
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
    <div class="modal fade servicios-modal" id="modalVer" tabindex="-1" aria-labelledby="tituloVer" aria-hidden="true"
         data-buscar-url="{{ route('servicios.buscar', ['id' => '__ID__']) }}">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="servicios-modal-header">
                    <div class="servicios-modal-heading">
                        <div class="servicios-modal-icon"><span class="material-symbols-outlined">visibility</span></div>
                        <div>
                            <h2 class="servicios-modal-title" id="tituloVer">Detalle de servicio</h2>
                            <p class="servicios-modal-subtitle">Información completa del registro.</p>
                        </div>
                    </div>
                    <button type="button" class="servicios-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                <div class="modal-body servicios-modal-body">
                    <dl class="servicios-detail-grid">
                        <div class="servicios-detail-item"><dt>Identificador</dt><dd data-ver="id">—</dd></div>
                        <div class="servicios-detail-item"><dt>Servicio</dt><dd data-ver="servicio">—</dd></div>
                        <div class="servicios-detail-item is-wide"><dt>Descripción</dt><dd data-ver="descripcion">—</dd></div>
                    </dl>
                </div>

                <div class="servicios-modal-footer">
                    <button type="button" class="btn btn-tonal" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: ELIMINAR --}}
    @if($puedeEliminar)
    <div class="modal fade servicios-modal servicios-delete-modal" id="modalEliminar" tabindex="-1" aria-labelledby="tituloEliminar" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="servicios-delete-content">
                    <div class="servicios-delete-icon"><span class="material-symbols-outlined">delete</span></div>

                    <button type="button" class="servicios-modal-close servicios-delete-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>

                    <div class="servicios-delete-heading">
                        <span class="servicios-delete-overline">Confirmación requerida</span>
                        <h2 id="tituloEliminar">¿Eliminar este servicio?</h2>
                        <p>Esta acción no se puede deshacer desde la interfaz.</p>
                    </div>

                    <form id="formEliminar" method="POST"
                          data-url="{{ route('servicios.eliminar', ['servicio' => '__ID__']) }}" novalidate>
                        @csrf
                        @method('DELETE')
                        <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                        <div class="servicios-delete-identity">
                            <div class="servicios-delete-avatar"><span class="material-symbols-outlined">medical_services</span></div>
                            <div class="servicios-delete-data">
                                <span class="servicios-delete-name" id="eliminar_nombre">—</span>
                                <span class="servicios-delete-sub" id="eliminar_sub">—</span>
                                <span class="servicios-delete-id">Identificador: #<span id="eliminar_id">—</span></span>
                            </div>
                        </div>

                        <div class="servicios-delete-notice">
                            <span class="material-symbols-outlined">info</span>
                            <p>No elimines servicios que ya hayan sido facturados: se conserva el historial.</p>
                        </div>

                        <div class="servicios-delete-actions">
                            <button type="button" class="btn btn-tonal" data-bs-dismiss="modal">Conservar</button>
                            <button type="submit" class="btn servicios-delete-confirm">
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
    VISTA: CATÁLOGO DE SERVICIOS
    ------------------------------------------------------------
    OBJETIVO:
    Permitir consultar y administrar los servicios médicos o
    profesionales que ofrece la clínica y que posteriormente
    pueden utilizarse en el proceso de facturación.

    CONTENIDO GENERAL:
    1. Encabezado:
       - Título "Servicios".
       - Descripción de la sección.
       - Botón para registrar un servicio, según los permisos
         del usuario autenticado.

    2. Tabla de servicios:
       - Identificador.
       - Nombre del servicio.
       - Descripción.
       - Precio.
       - Acciones disponibles.

    3. Filtros:
       - Búsqueda por nombre.
       - Búsqueda por descripción.
       - Filtro por precio, si resulta necesario.

    4. Modal de registro:
       - Campo para el nombre del servicio.
       - Campo para la descripción.
       - Campo para el precio.
       - Botón para guardar.

    5. Modal de edición:
       - Cargar la información del servicio seleccionado.
       - Permitir actualizar nombre, descripción y precio.
       - Validar que el precio sea un valor numérico válido
         y no negativo.

    6. Modal de visualización:
       - Mostrar los datos completos del servicio.
       - Mostrar su precio actual.

    REGLAS DE NEGOCIO:
    - No permitir nombres vacíos.
    - Validar la longitud máxima de los campos según la migración.
    - El precio debe ser numérico y no negativo.
    - Los servicios se utilizan como referencia al crear
      los detalles de una factura.
    - Si un servicio ya fue utilizado en una factura, no se debe
      alterar el precio histórico de dicha factura al modificar
      el precio actual del catálogo.
    - Evitar eliminar físicamente servicios que estén relacionados
      con registros históricos.
    - Restringir las operaciones administrativas según el rol.

    INTEGRACIÓN:
    - Utilizar ServicioController.
    - Consultar y administrar los servicios mediante las rutas
      existentes del módulo servicios.
    - La información debe estar disponible para el módulo
      de facturación.
    - Los cálculos y validaciones importantes deben realizarse
      en el backend.
--}}
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('js/servicio/servicios.js') }}"></script>
@endpush
