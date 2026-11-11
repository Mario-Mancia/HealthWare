{{-- HEALTHWARE-GENERADO: generado por generar-frontend.sh v1.0.0 · no mezclar con otros marcadores --}}
@extends('layouts.app_layout')

@section('titulo', 'Factura')

@push('estilos')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('css/facturacion.css') }}">
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

    <div class="facturacion-page">

        {{-- ENCABEZADO --}}
        <header class="facturacion-header">
            <div class="facturacion-heading">
                <div class="facturacion-heading-icon">
                    <span class="material-symbols-outlined">receipt_long</span>
                </div>
                <div>
                    <h1 class="facturacion-title">Facturación</h1>
                    <p class="facturacion-subtitle">Facturas emitidas a los pacientes.</p>
                </div>
            </div>
        </header>

        <div class="facturacion-stats">
          <div class="facturacion-stat">
            <span class="material-symbols-outlined">receipt_long</span>
            <div><strong>{{ $lista->count() }}</strong><span>Facturas</span></div>
          </div>
          <div class="facturacion-stat">
            <span class="material-symbols-outlined">payments</span>
            <div><strong>{{ "$ " . number_format((float) $lista->sum("total"), 2) }}</strong><span>Total facturado</span></div>
          </div>
        </div>


        {{-- SUPERFICIE --}}
        <section class="facturacion-surface">

            <div class="facturacion-surface-header">
                <div>
                    <h2 class="facturacion-surface-title">Facturas emitidas</h2>
                    <p class="facturacion-surface-description">Consulta las facturas y su estado de pago.</p>
                </div>
                <div class="facturacion-record-indicator">
                    <span class="material-symbols-outlined">database</span>
                    <span>{{ $lista->count() }} registros</span>
                </div>
            </div>

            @if($lista->isEmpty())
                <div class="facturacion-empty">
                    <span class="material-symbols-outlined">receipt_long</span>
                    <h3>Aún no hay registros</h3>
                    <p>Cuando existan datos aparecerán aquí.</p>
                </div>
            @else
            <div class="facturacion-toolbar">
                <select class="form-select" data-filtro-columna="4" aria-label="Filtrar por estado">
                    <option value="">Estado: todos</option>
                </select>
                <button type="button" class="btn btn-text facturacion-toolbar-clear" data-filtros-limpiar>Limpiar filtros</button>
            </div>

            <div class="table-responsive facturacion-table-container">
                <table id="tabla-facturacion" class="table facturacion-table align-middle mb-0" data-page-length="10">
                    <thead>
                        <tr>
                            <th scope="col" class="facturacion-column-id">ID</th>
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
                            <td class="facturacion-id">#{{ $item->id }}</td>
                            <td class="facturacion-mono">{{ $item->numero_factura ?? "—" }}</td>
                            <td>
                              <div class="facturacion-identity">
                                <div class="facturacion-avatar"><span class="material-symbols-outlined">person</span></div>
                                <div class="facturacion-identity-data">
                                  <span class="facturacion-fullname" data-filtro-valor>{{ ($item->paciente?->nombres ?? '') . ' ' . ($item->paciente?->apellidos ?? '') }}</span>
                                  <span class="facturacion-identity-caption">{{ \Illuminate\Support\Str::limit((string) ($fmtFecha($item->fecha_emision) ?? ""), 60) }}</span>
                                </div>
                              </div>
                            </td>
                            <td>{{ $fmtFecha($item->fecha_emision, 'd/m/Y') }}</td>
                            <td><span class="facturacion-status facturacion-status-{{ ['pagada' => 'success', 'pagado' => 'success', 'pendiente' => 'warning', 'anulada' => 'error', 'cancelada' => 'error', 'emitida' => 'info', ][(string) (strtolower((string) $item->estado))] ?? "neutral" }}" data-filtro-valor><span class="facturacion-status-dot"></span>{{ ucfirst(str_replace("_", " ", (string) (strtolower((string) $item->estado)))) }}</span></td>
                            <td>$ {{ number_format((float) $item->total, 2) }}</td>
                            <td>
                                <div class="facturacion-actions">
                                    <button type="button" class="facturacion-action-button"
                                            data-bs-toggle="modal" data-bs-target="#modalVer"
                                            data-id="{{ $item->id }}"
                                            title="Consultar factura" aria-label="Consultar factura">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </button>
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

    {{-- MODAL: CONSULTA --}}
    <div class="modal fade facturacion-modal" id="modalVer" tabindex="-1" aria-labelledby="tituloVer" aria-hidden="true"
         data-buscar-url="{{ route('facturas.buscar', ['id' => '__ID__']) }}">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="facturacion-modal-header">
                    <div class="facturacion-modal-heading">
                        <div class="facturacion-modal-icon"><span class="material-symbols-outlined">visibility</span></div>
                        <div>
                            <h2 class="facturacion-modal-title" id="tituloVer">Detalle de factura</h2>
                            <p class="facturacion-modal-subtitle">Información completa del registro.</p>
                        </div>
                    </div>
                    <button type="button" class="facturacion-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                <div class="modal-body facturacion-modal-body">
                    <dl class="facturacion-detail-grid">
                        <div class="facturacion-detail-item"><dt>N.º de factura</dt><dd data-ver="numero_factura">—</dd></div>
                        <div class="facturacion-detail-item"><dt>Paciente</dt><dd data-ver="paciente.nombres+paciente.apellidos">—</dd></div>
                        <div class="facturacion-detail-item"><dt>Fecha de emisión</dt><dd data-ver="fecha_emision" data-formato="fechahora">—</dd></div>
                        <div class="facturacion-detail-item"><dt>Estado</dt><dd data-ver="estado">—</dd></div>
                        <div class="facturacion-detail-item"><dt>Subtotal</dt><dd data-ver="subtotal" data-formato="dinero">—</dd></div>
                        <div class="facturacion-detail-item"><dt>Impuesto</dt><dd data-ver="impuesto" data-formato="dinero">—</dd></div>
                        <div class="facturacion-detail-item"><dt>Descuento</dt><dd data-ver="descuento" data-formato="dinero">—</dd></div>
                        <div class="facturacion-detail-item"><dt>Total</dt><dd data-ver="total" data-formato="dinero">—</dd></div>
                        <div class="facturacion-detail-item is-wide"><dt>Notas</dt><dd data-ver="notas">—</dd></div>
                    </dl>
                </div>

                <div class="facturacion-modal-footer">
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
@endpush
