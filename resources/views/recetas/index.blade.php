{{-- HEALTHWARE-GENERADO: generado por generar-frontend.sh v1.0.0 · no mezclar con otros marcadores --}}
@extends('layouts.app_layout')

@section('titulo', 'Recetas')

@push('estilos')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endpush

@section('contenido')

    @php
        $rolActual = \Illuminate\Support\Str::of(auth()->user()?->rol?->rol ?? '')->ascii()->lower()->toString();
        $puedeCrear = false;
        $puedeEditar = false;
        $puedeEliminar = false;
        $lista = $recetas ?? collect();
        $fmtFecha = fn($v, $f = 'd/m/Y') => $v ? \Illuminate\Support\Carbon::parse($v)->format($f) : '—';
    @endphp

    <div class="hw-page">

        {{-- ENCABEZADO --}}
        <header class="hw-header">
            <div class="hw-heading">
                <div class="hw-heading-icon">
                    <span class="material-symbols-outlined">prescriptions</span>
                </div>
                <div>
                    <h1 class="hw-title">Recetas médicas</h1>
                    <p class="hw-subtitle">Consulta de recetas emitidas y sus medicamentos.</p>
                </div>
            </div>
        </header>


        {{-- SUPERFICIE --}}
        <section class="hw-surface">

            <div class="hw-surface-header">
                <div>
                    <h2 class="hw-surface-title">Recetas emitidas</h2>
                    <p class="hw-surface-description">Consulta cada receta y el detalle de sus medicamentos.</p>
                </div>
                <div class="hw-record-indicator">
                    <span class="material-symbols-outlined">database</span>
                    <span>{{ $lista->count() }} registros</span>
                </div>
            </div>

            @if($lista->isEmpty())
                <div class="hw-empty">
                    <span class="material-symbols-outlined">prescriptions</span>
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
                <select class="form-select" data-filtro-columna="3" aria-label="Filtrar por doctor">
                    <option value="">Doctor: todos</option>
                </select>
                <span class="hw-toolbar-count" data-hw-contador></span>
                <button type="button" class="btn btn-text hw-toolbar-clear" data-filtros-limpiar>Limpiar filtros</button>
            </div>

            <div class="table-responsive hw-table-container">
                <table id="tabla-recetas" class="table hw-data-table align-middle mb-0" data-page-length="10">
                    <thead>
                        <tr>
                            <th scope="col" class="hw-column-id">ID</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Paciente</th>
                            <th scope="col">Doctor</th>
                            <th scope="col">Notas</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($lista as $item)
                        <tr>
                            <td class="hw-id">#{{ $item->id }}</td>
                            <td>{{ $fmtFecha($item->fecha_prescripcion, 'd/m/Y H:i') }}</td>
                            <td>
                              <div class="hw-identity">
                                <div class="hw-avatar"><span class="material-symbols-outlined">person</span></div>
                                <div class="hw-identity-data">
                                  <span class="hw-fullname" data-filtro-valor>{{ ($item->registroMedico?->paciente?->nombres ?? '') . ' ' . ($item->registroMedico?->paciente?->apellidos ?? '') }}</span>
                                  <span class="hw-identity-caption">{{ \Illuminate\Support\Str::limit((string) ('Registro #' . $item->id_registro_medico ?? ""), 60) }}</span>
                                </div>
                              </div>
                            </td>
                            <td>{{ ($item->registroMedico?->doctor?->usuario?->nombres ?? '') . ' ' . ($item->registroMedico?->doctor?->usuario?->apellidos ?? '') ?? "—" }}</td>
                            <td class="hw-muted" title="{{ $item->notas }}">{{ \Illuminate\Support\Str::limit((string) ($item->notas ?? ""), 70) ?: "—" }}</td>
                            <td>
                                <div class="hw-actions">
                                    <button type="button" class="hw-action-button"
                                            data-bs-toggle="modal" data-bs-target="#modalVer"
                                            data-id="{{ $item->id }}"
                                            title="Consultar receta" aria-label="Consultar receta">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </button>
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

    {{-- MODAL: CONSULTA --}}
    <div class="modal fade hw-modal" id="modalVer" tabindex="-1" aria-labelledby="tituloVer" aria-hidden="true"
         data-buscar-url="{{ route('recetas.buscar', ['id' => '__ID__']) }}">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="hw-modal-header">
                    <div class="hw-modal-heading">
                        <div class="hw-modal-icon"><span class="material-symbols-outlined">visibility</span></div>
                        <div>
                            <h2 class="hw-modal-title" id="tituloVer">Detalle de receta</h2>
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
                        <div class="hw-detail-item"><dt>Paciente</dt><dd data-ver="registro_medico.paciente.nombres+registro_medico.paciente.apellidos">—</dd></div>
                        <div class="hw-detail-item"><dt>Doctor responsable</dt><dd data-ver="registro_medico.doctor.usuario.nombres+registro_medico.doctor.usuario.apellidos">—</dd></div>
                        <div class="hw-detail-item"><dt>Fecha de prescripción</dt><dd data-ver="fecha_prescripcion" data-formato="fechahora">—</dd></div>
                        <div class="hw-detail-item"><dt>Registro médico</dt><dd data-ver="id_registro_medico">—</dd></div>
                        <div class="hw-detail-item is-wide"><dt>Notas</dt><dd data-ver="notas">—</dd></div>
                        <div class="hw-detail-item is-wide"><dt>Medicamentos</dt><dd data-ver="detalles" data-formato="lista" data-columnas="Medicamento:medicamento.nombre;Dosis:dosis;Frecuencia:frecuencia;Duración:duracion;Vía:via_administracion;Indicaciones:instrucciones">—</dd></div>
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
        VISTA: Consulta y gestión general de recetas médicas.

        OBJETIVO:
        Proporcionar una vista para consultar recetas registradas y
        acceder a sus respectivos detalles.

        CONTENIDO:
        1. ENCABEZADO:
           - Título "Recetas médicas".
           - Botón para emitir una receta, según permisos.

        2. FILTROS:
           - Paciente.
           - Doctor.
           - Fecha de emisión.
           - Estado de la receta, si está contemplado.

        3. TABLA:
           - Identificador de receta.
           - Paciente.
           - Doctor responsable.
           - Fecha de emisión.
           - Cantidad de medicamentos.
           - Acciones.

        4. FORMULARIO DE RECETA:
           - Seleccionar paciente.
           - Asociar registro médico, cuando corresponda.
           - Seleccionar doctor responsable.
           - Fecha de emisión.
           - Indicaciones generales.

        5. DETALLE DE MEDICAMENTOS:
           - Medicamento.
           - Dosis.
           - Frecuencia.
           - Duración.
           - Cantidad prescrita.
           - Indicaciones particulares.

        6. ACCIONES:
           - Consultar receta.
           - Agregar medicamentos.
           - Consultar expediente del paciente.
           - Preparar una versión imprimible de la receta.

        CONSIDERACIONES:
        - Las recetas_items representan los medicamentos de cada receta.
        - La creación de la receta y sus detalles debe mantener integridad
          mediante una transacción cuando corresponda.
        - El catálogo de medicamentos debe proporcionar los productos
          seleccionables.
        - El médico es responsable de las operaciones clínicas.
        - Esta vista puede funcionar como listado general, mientras que
          el expediente del paciente muestra su historial individual.
    --}}
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('js/recetas/recetas.js') }}"></script>
@endpush
