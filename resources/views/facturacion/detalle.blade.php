@extends('layouts.app_layout')

@section('titulo', 'Detalle de factura')

@section('contenido')

    @php
        // La factura la entrega el controlador (relaciones esperadas: paciente, items y pagos).
        $factura = $factura ?? null;
        $fmt = fn ($v, $f = 'd/m/Y') => $v ? \Illuminate\Support\Carbon::parse($v)->format($f) : '—';

        $lineas = collect($factura->items ?? $factura->facturaItems ?? $factura->detalles ?? []);
        $pagos = collect($factura->pagos ?? []);
        $estadoFactura = strtolower((string) ($factura->estado ?? ''));
        $claseEstado = ['pagada' => 'success', 'pagado' => 'success', 'pendiente' => 'warning', 'anulada' => 'error', 'cancelada' => 'error', 'emitida' => 'info'][$estadoFactura] ?? 'neutral';
        $nombrePaciente = trim(($factura->paciente?->nombres ?? '') . ' ' . ($factura->paciente?->apellidos ?? ''));

        $subtotalFactura = $lineas->sum(fn ($i) => (float) ($i->subtotal ?? ((float) ($i->cantidad ?? 0) * (float) ($i->precio_unitario ?? 0))));
        $totalFactura = (float) ($factura->total ?? $subtotalFactura);
        $pagadoFactura = (float) $pagos->sum('monto');
    @endphp

    <div class="hw-page">

        <header class="hw-header">
            <div class="hw-heading">
                <div class="hw-heading-icon"><span class="material-symbols-outlined">receipt_long</span></div>
                <div>
                    <h1 class="hw-title">Detalle de factura</h1>
                    <p class="hw-subtitle">Documento de la factura, sus conceptos y su pago.</p>
                </div>
            </div>

            <div class="hw-document-actions">
                @if(\Illuminate\Support\Facades\Route::has('facturas'))
                    <a href="{{ route('facturas') }}" class="btn btn-tonal">
                        <span class="material-symbols-outlined">arrow_back</span>
                        Volver al listado
                    </a>
                @endif

                @if($factura)
                    <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                        <span class="material-symbols-outlined">print</span>
                        Imprimir
                    </button>

                    @if(\Illuminate\Support\Facades\Route::has('facturas.pdf'))
                        <a href="{{ route('facturas.pdf', $factura->id) }}" class="btn btn-primary" target="_blank" rel="noopener">
                            <span class="material-symbols-outlined">picture_as_pdf</span>
                            Exportar a PDF
                        </a>
                    @endif
                @endif
            </div>
        </header>

        @if(! $factura)

            <section class="hw-surface">
                <div class="hw-empty">
                    <span class="material-symbols-outlined">receipt_long</span>
                    <h3>No se encontró la factura</h3>
                    <p>El controlador todavía no envió la factura a esta vista.</p>
                </div>
            </section>

        @else

            <article class="hw-document">

                <div class="hw-document-head">
                    <div class="hw-document-brand">
                        <span class="material-symbols-outlined">health_and_safety</span>
                        <div>
                            <strong>HealthWare</strong>
                            <span>Sistema de Gestión Clínica</span>
                        </div>
                    </div>

                    <div class="hw-document-meta">
                        <h2>Factura {{ $factura->numero_factura ?? ('#' . $factura->id) }}</h2>
                        <p>Emitida el {{ $fmt($factura->fecha_emision) }}</p>
                        <span class="hw-status hw-status-{{ $claseEstado }}">
                            <span class="hw-status-dot"></span>{{ ucfirst(str_replace('_', ' ', $estadoFactura ?: 'sin estado')) }}
                        </span>
                    </div>
                </div>

                <div class="hw-document-parties">
                    <div class="hw-document-party">
                        <h3>Emisor</h3>
                        <p><strong>HealthWare</strong></p>
                        <p>Sistema de Gestión Clínica</p>
                    </div>
                    <div class="hw-document-party">
                        <h3>Paciente</h3>
                        <p><strong>{{ $nombrePaciente ?: '—' }}</strong></p>
                        @if(! empty($factura->paciente?->dui))<p>DUI: {{ $factura->paciente->dui }}</p>@endif
                        @if(! empty($factura->paciente?->telefono))<p>Tel.: {{ $factura->paciente->telefono }}</p>@endif
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table hw-data-table align-middle">
                        <thead>
                            <tr>
                                <th>Concepto</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Precio unitario</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lineas as $linea)
                                @php
                                    $cantidadLinea = (float) ($linea->cantidad ?? 0);
                                    $precioLinea = (float) ($linea->precio_unitario ?? 0);
                                @endphp
                                <tr>
                                    <td>
                                        <span class="hw-fullname">{{ $linea->servicio?->servicio ?? $linea->descripcion ?? 'Servicio' }}</span>
                                        @if(! empty($linea->descripcion) && ($linea->servicio?->servicio ?? null))
                                            <span class="hw-identity-caption d-block">{{ $linea->descripcion }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ rtrim(rtrim(number_format($cantidadLinea, 2), '0'), '.') }}</td>
                                    <td class="text-end">$ {{ number_format($precioLinea, 2) }}</td>
                                    <td class="text-end">$ {{ number_format((float) ($linea->subtotal ?? $cantidadLinea * $precioLinea), 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center hw-muted py-4">Sin conceptos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="hw-document-totals">
                    <div><span>Subtotal</span><span>$ {{ number_format($subtotalFactura, 2) }}</span></div>
                    @if($pagos->isNotEmpty())
                        <div><span>Pagado</span><span>$ {{ number_format($pagadoFactura, 2) }}</span></div>
                    @endif
                    <div class="is-total"><span>Total</span><span>$ {{ number_format($totalFactura, 2) }}</span></div>
                </div>

                @if($pagos->isNotEmpty())
                    <h3 class="h6 mt-4 mb-2">Pago registrado</h3>
                    <div class="table-responsive">
                        <table class="table hw-data-table align-middle mb-0">
                            <thead><tr><th>Fecha</th><th>Método</th><th>Referencia</th><th class="text-end">Monto</th></tr></thead>
                            <tbody>
                                @foreach($pagos as $pago)
                                    <tr>
                                        <td>{{ $fmt($pago->fecha_pago ?? $pago->created_at) }}</td>
                                        <td>{{ ucfirst((string) ($pago->metodo_pago ?? $pago->metodo ?? '—')) }}</td>
                                        <td>{{ $pago->referencia ?? $pago->referencia_pago ?? '—' }}</td>
                                        <td class="text-end">$ {{ number_format((float) ($pago->monto ?? 0), 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            </article>

        @endif

    </div>

    {{--
        VISTA: Detalle individual de una factura (imprimible).
        Variables esperadas: $factura con relaciones paciente, items (o facturaItems/detalles) y pagos.
        La exportación a PDF se activa sola cuando exista la ruta nombrada "facturas.pdf".
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/facturacion/facturas.js') }}"></script>
@endpush
