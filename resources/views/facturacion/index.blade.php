{{-- HEALTHWARE-GENERADO: generado por generar-frontend.sh v1.0.0 · no mezclar con otros marcadores --}}
@extends('layouts.app_layout')

@section('titulo', 'Factura')

@push('estilos')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endpush

@section('contenido')

    @php
        $rolActual = \Illuminate\Support\Str::of(auth()->user()?->rol?->rol ?? '')->ascii()->lower()->toString();
        $puedeCrear = false;
        $puedeEditar = false;
        $puedeEliminar = false;
        $lista = $facturas ?? collect();
        $fmtFecha = fn($v, $f = 'd/m/Y') => $v ? \Illuminate\Support\Carbon::parse($v)->format($f) : '—';
    @endphp

    <div class="hw-page">

        {{-- ENCABEZADO --}}
        <header class="hw-header">
            <div class="hw-heading">
                <div class="hw-heading-icon">
                    <span class="material-symbols-outlined">receipt_long</span>
                </div>
                <div>
                    <h1 class="hw-title">Facturación</h1>
                    <p class="hw-subtitle">Facturas emitidas a los pacientes.</p>
                </div>
            </div>

            @if(\Illuminate\Support\Facades\Route::has('facturas.insertar') && in_array($rolActual, ['administrador', 'recepcionista']))
                <button type="button" class="btn btn-primary hw-create-button" data-bs-toggle="modal" data-bs-target="#modalEmitir">
                    <span class="material-symbols-outlined">add_circle</span>
                    Emitir factura
                </button>
            @endif
        </header>

        <div class="hw-stats">
          <div class="hw-stat">
            <span class="material-symbols-outlined">receipt_long</span>
            <div><strong>{{ $lista->count() }}</strong><span>Facturas</span></div>
          </div>
          <div class="hw-stat">
            <span class="material-symbols-outlined">payments</span>
            <div><strong>{{ "$ " . number_format((float) $lista->sum("total"), 2) }}</strong><span>Total facturado</span></div>
          </div>
          <div class="hw-stat">
            <span class="material-symbols-outlined">task_alt</span>
            <div><strong>{{ $lista->filter(fn ($f) => in_array(strtolower((string) $f->estado), ['pagada', 'pagado']))->count() }}</strong><span>Pagadas</span></div>
          </div>
          <div class="hw-stat">
            <span class="material-symbols-outlined">pending_actions</span>
            <div><strong>{{ $lista->filter(fn ($f) => in_array(strtolower((string) $f->estado), ['pendiente', 'emitida']))->count() }}</strong><span>Pendientes</span></div>
          </div>
        </div>


        {{-- SUPERFICIE --}}
        <section class="hw-surface">

            <div class="hw-surface-header">
                <div>
                    <h2 class="hw-surface-title">Facturas emitidas</h2>
                    <p class="hw-surface-description">Consulta las facturas y su estado de pago.</p>
                </div>
                <div class="hw-record-indicator">
                    <span class="material-symbols-outlined">database</span>
                    <span>{{ $lista->count() }} registros</span>
                </div>
            </div>

            @if($lista->isEmpty())
                <div class="hw-empty">
                    <span class="material-symbols-outlined">receipt_long</span>
                    <h3>Aún no hay registros</h3>
                    <p>Cuando existan datos aparecerán aquí.</p>
                </div>
            @else
            <div class="hw-toolbar">
                <div class="hw-search">
                    <span class="material-symbols-outlined">search</span>
                    <input type="search" class="form-control" data-hw-buscar placeholder="Buscar en la tabla…" aria-label="Buscar en la tabla" autocomplete="off">
                    <button type="button" class="btn btn-icon hw-search-clear" data-hw-buscar-limpiar aria-label="Limpiar búsqueda">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <select class="form-select" data-filtro-columna="4" aria-label="Filtrar por estado">
                    <option value="">Estado: todos</option>
                </select>
                <span class="hw-toolbar-count" data-hw-contador></span>
                <button type="button" class="btn btn-text hw-toolbar-clear" data-filtros-limpiar>Limpiar filtros</button>
            </div>

            <div class="table-responsive hw-table-container">
                <table id="tabla-facturacion" class="table hw-data-table align-middle mb-0" data-page-length="10">
                    <thead>
                        <tr>
                            <th scope="col" class="hw-column-id">ID</th>
                            <th scope="col">N.º factura</th>
                            <th scope="col">Paciente</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Total</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($lista as $item)
                        <tr>
                            <td class="hw-id">#{{ $item->id }}</td>
                            <td class="hw-mono">{{ $item->numero_factura ?? "—" }}</td>
                            <td>
                              <div class="hw-identity">
                                <div class="hw-avatar"><span class="material-symbols-outlined">person</span></div>
                                <div class="hw-identity-data">
                                  <span class="hw-fullname" data-filtro-valor>{{ ($item->paciente?->nombres ?? '') . ' ' . ($item->paciente?->apellidos ?? '') }}</span>
                                  <span class="hw-identity-caption">{{ \Illuminate\Support\Str::limit((string) ($fmtFecha($item->fecha_emision) ?? ""), 60) }}</span>
                                </div>
                              </div>
                            </td>
                            <td>{{ $fmtFecha($item->fecha_emision, 'd/m/Y') }}</td>
                            <td><span class="hw-status hw-status-{{ ['pagada' => 'success', 'pagado' => 'success', 'pendiente' => 'warning', 'anulada' => 'error', 'cancelada' => 'error', 'emitida' => 'info', ][(string) (strtolower((string) $item->estado))] ?? "neutral" }}" data-filtro-valor><span class="hw-status-dot"></span>{{ ucfirst(str_replace("_", " ", (string) (strtolower((string) $item->estado)))) }}</span></td>
                            <td>$ {{ number_format((float) $item->total, 2) }}</td>
                            <td>
                                <div class="hw-actions">
                                    <button type="button" class="hw-action-button"
                                            data-bs-toggle="modal" data-bs-target="#modalVer"
                                            data-id="{{ $item->id }}"
                                            title="Consultar factura" aria-label="Consultar factura">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </button>
                                    @if(\Illuminate\Support\Facades\Route::has('detalle_facturas'))
                                        <a href="{{ route('detalle_facturas', $item->id) }}" class="hw-action-button"
                                           title="Ver factura completa" aria-label="Ver factura completa">
                                            <span class="material-symbols-outlined">description</span>
                                        </a>
                                    @endif
                                    @if(\Illuminate\Support\Facades\Route::has('facturas.pdf'))
                                        <a href="{{ route('facturas.pdf', $item->id) }}" class="hw-action-button"
                                           title="Exportar a PDF" aria-label="Exportar a PDF" target="_blank" rel="noopener">
                                            <span class="material-symbols-outlined">picture_as_pdf</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="hw-no-results">
                <span class="material-symbols-outlined">search_off</span>
                No se encontraron coincidencias con la búsqueda.
            </div>
            @endif

        </section>
    </div>


    {{-- MODAL: EMITIR FACTURA CON PAGO ÚNICO (solo si existe la ruta facturas.insertar) --}}
    @if(\Illuminate\Support\Facades\Route::has('facturas.insertar') && in_array($rolActual, ['administrador', 'recepcionista']))
    <div class="modal fade hw-modal" id="modalEmitir" tabindex="-1" aria-labelledby="tituloEmitir" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="hw-modal-header">
                    <div class="hw-modal-heading">
                        <div class="hw-modal-icon"><span class="material-symbols-outlined">receipt_long</span></div>
                        <div>
                            <h2 class="hw-modal-title" id="tituloEmitir">Emitir factura</h2>
                            <p class="hw-modal-subtitle">La factura se emite y se cobra en un solo paso.</p>
                        </div>
                    </div>
                    <button type="button" class="hw-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="formNuevo" method="POST" action="{{ route('facturas.insertar') }}" novalidate data-hw-factura>
                    @csrf
                    <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                    <div class="modal-body hw-modal-body">

                        <div class="hw-form-section">
                            <div class="hw-form-section-heading">
                                <span class="material-symbols-outlined">person</span>
                                <div>
                                    <h3>Paciente y emisión</h3>
                                    <p>A quién se factura y en qué fecha.</p>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="form-floating">
                                        <select name="id_paciente" id="nuevo_id_paciente" class="form-select" required>
                                            <option value="" disabled selected></option>
                                            @foreach(($pacientes ?? collect()) as $pacienteFactura)
                                                <option value="{{ $pacienteFactura->id }}">{{ trim(($pacienteFactura->nombres ?? '') . ' ' . ($pacienteFactura->apellidos ?? '')) }}</option>
                                            @endforeach
                                        </select>
                                        <label for="nuevo_id_paciente">Paciente</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="date" name="fecha_emision" id="nuevo_fecha_emision" class="form-control"
                                               value="{{ now()->format('Y-m-d') }}" placeholder="Fecha de emisión" required>
                                        <label for="nuevo_fecha_emision">Fecha de emisión</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="hw-form-section">
                            <div class="hw-form-section-heading">
                                <span class="material-symbols-outlined">list_alt</span>
                                <div>
                                    <h3>Conceptos facturados</h3>
                                    <p>Servicios prestados, con cantidad y precio unitario.</p>
                                </div>
                            </div>

                            <div class="hw-invoice-lines" data-hw-lineas></div>

                            <button type="button" class="btn btn-tonal mt-3" data-hw-agregar-linea>
                                <span class="material-symbols-outlined">add</span>
                                Agregar concepto
                            </button>

                            <template id="hwLineaPlantilla">
                                <div class="hw-invoice-line" data-hw-linea>
                                    <div class="form-floating">
                                        <select class="form-select" name="items[__I__][id_servicio]" data-hw-servicio required>
                                            <option value="" disabled selected></option>
                                            @foreach(($servicios ?? collect()) as $servicioFactura)
                                                <option value="{{ $servicioFactura->id }}"
                                                        data-precio="{{ $servicioFactura->precio ?? $servicioFactura->precio_base ?? 0 }}">
                                                    {{ $servicioFactura->servicio ?? $servicioFactura->nombre ?? ('Servicio #' . $servicioFactura->id) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label>Servicio</label>
                                    </div>
                                    <div class="form-floating">
                                        <input type="number" class="form-control" name="items[__I__][cantidad]" min="1" step="1" value="1" placeholder="Cantidad" data-hw-cantidad required>
                                        <label>Cantidad</label>
                                    </div>
                                    <div class="form-floating">
                                        <input type="number" class="form-control" name="items[__I__][precio_unitario]" min="0" step="0.01" placeholder="Precio unitario" data-hw-precio required>
                                        <label>Precio unitario</label>
                                    </div>
                                    <button type="button" class="btn btn-icon" data-hw-quitar-linea aria-label="Quitar concepto">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <div class="hw-form-section">
                            <div class="hw-form-section-heading">
                                <span class="material-symbols-outlined">payments</span>
                                <div>
                                    <h3>Pago único</h3>
                                    <p>El pago se registra al emitir la factura.</p>
                                </div>
                            </div>
                            <div class="row g-3 align-items-start">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="metodo_pago" id="nuevo_metodo_pago" class="form-select" required>
                                            <option value="" disabled selected></option>
                                            <option value="efectivo">Efectivo</option>
                                            <option value="tarjeta">Tarjeta</option>
                                            <option value="transferencia">Transferencia</option>
                                        </select>
                                        <label for="nuevo_metodo_pago">Método de pago</label>
                                    </div>
                                    <div class="form-floating mt-3">
                                        <input type="text" name="referencia_pago" id="nuevo_referencia_pago" class="form-control" placeholder="Referencia" maxlength="100">
                                        <label for="nuevo_referencia_pago">Referencia (opcional)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="hw-invoice-summary" aria-live="polite">
                                        <div><span>Subtotal</span><span data-hw-subtotal>$ 0.00</span></div>
                                        <div><span>Total a cobrar</span><span data-hw-total>$ 0.00</span></div>
                                    </div>
                                    <input type="hidden" name="subtotal" data-hw-campo-subtotal value="0">
                                    <input type="hidden" name="total" data-hw-campo-total value="0">
                                    <input type="hidden" name="monto_pagado" data-hw-campo-pagado value="0">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="hw-modal-footer">
                        <button type="button" class="btn btn-tonal" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="material-symbols-outlined">task_alt</span>
                            Emitir y registrar pago
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL: CONSULTA --}}
    <div class="modal fade hw-modal" id="modalVer" tabindex="-1" aria-labelledby="tituloVer" aria-hidden="true"
         data-buscar-url="{{ route('facturas.buscar', ['id' => '__ID__']) }}">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="hw-modal-header">
                    <div class="hw-modal-heading">
                        <div class="hw-modal-icon"><span class="material-symbols-outlined">visibility</span></div>
                        <div>
                            <h2 class="hw-modal-title" id="tituloVer">Detalle de factura</h2>
                            <p class="hw-modal-subtitle">Información completa del registro.</p>
                        </div>
                    </div>
                    <button type="button" class="hw-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                <div class="modal-body hw-modal-body">
                    <dl class="hw-detail-grid">
                        <div class="hw-detail-item"><dt>N.º de factura</dt><dd data-ver="numero_factura">—</dd></div>
                        <div class="hw-detail-item"><dt>Paciente</dt><dd data-ver="paciente.nombres+paciente.apellidos">—</dd></div>
                        <div class="hw-detail-item"><dt>Fecha de emisión</dt><dd data-ver="fecha_emision" data-formato="fechahora">—</dd></div>
                        <div class="hw-detail-item"><dt>Estado</dt><dd data-ver="estado">—</dd></div>
                        <div class="hw-detail-item"><dt>Subtotal</dt><dd data-ver="subtotal" data-formato="dinero">—</dd></div>
                        <div class="hw-detail-item"><dt>Impuesto</dt><dd data-ver="impuesto" data-formato="dinero">—</dd></div>
                        <div class="hw-detail-item"><dt>Descuento</dt><dd data-ver="descuento" data-formato="dinero">—</dd></div>
                        <div class="hw-detail-item"><dt>Total</dt><dd data-ver="total" data-formato="dinero">—</dd></div>
                        <div class="hw-detail-item is-wide"><dt>Notas</dt><dd data-ver="notas">—</dd></div>
                    </dl>
                </div>

                <div class="hw-modal-footer">
                    <button type="button" class="btn btn-tonal" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== ESPECIFICACIÓN ORIGINAL (conservada por generar-frontend.sh) ===== --}}
    {{--
        VISTA: Gestión general de facturación.

        OBJETIVO:
        Centralizar la consulta y creación de facturas de los servicios
        proporcionados por la clínica.

        CONTENIDO:
        1. ENCABEZADO:
           - Título "Facturación".
           - Botón para generar una factura.

        2. INDICADORES:
           - Facturas emitidas.
           - Facturas pendientes.
           - Facturas pagadas.
           - Total facturado en el período.

        3. FILTROS:
           - Número de factura.
           - Paciente.
           - Fecha o rango de fechas.
           - Estado de la factura.

        4. TABLA:
           - Número o identificador de factura.
           - Paciente.
           - Fecha de emisión.
           - Total.
           - Estado.
           - Acciones.

        5. FORMULARIO / MODAL DE FACTURA:
           - Seleccionar paciente.
           - Fecha de emisión.
           - Agregar servicios.
           - Cantidad de cada servicio.
           - Precio unitario.
           - Subtotal.
           - Descuentos o impuestos, únicamente si están contemplados
             en las reglas de negocio.
           - Total calculado.

        6. ACCIONES:
           - Consultar detalle de factura.
           - Generar factura.
           - Consultar estado.
           - Acceder al proceso de pago cuando esté implementado.

        CONSIDERACIONES:
        - Los conceptos facturados proceden de facturas_items.
        - Los servicios se seleccionan desde el catálogo de servicios.
        - Los cálculos monetarios deben realizarse y validarse en el backend.
        - No permitir modificar arbitrariamente precios históricos.
        - La gestión corresponde al administrador y al recepcionista.
    --}}
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('js/facturacion/facturas.js') }}"></script>
    <script src="{{ asset('js/hw/hw-facturacion.js') }}"></script>
@endpush
