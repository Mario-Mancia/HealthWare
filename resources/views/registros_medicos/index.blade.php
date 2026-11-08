{{-- HEALTHWARE-GENERADO: generado por generar-frontend.sh v1.0.0 · no mezclar con otros marcadores --}}
@extends('layouts.app_layout')

@section('titulo', 'Registros Médicos')

@push('estilos')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('css/registros_medicos.css') }}">
@endpush

@section('contenido')

    @php
        $rolActual = \Illuminate\Support\Str::of(auth()->user()?->rol?->rol ?? '')->ascii()->lower()->toString();
        $puedeCrear = in_array($rolActual, ['administrador','medico']);
        $puedeEditar = false;
        $puedeEliminar = in_array($rolActual, ['administrador','medico']);
        $lista = $registrosMedicos ?? collect();
        $fmtFecha = fn($v, $f = 'd/m/Y') => $v ? \Illuminate\Support\Carbon::parse($v)->format($f) : '—';
    @endphp

    <div class="registros-medicos-page">

        {{-- ENCABEZADO --}}
        <header class="registros-medicos-header">
            <div class="registros-medicos-heading">
                <div class="registros-medicos-heading-icon">
                    <span class="material-symbols-outlined">clinical_notes</span>
                </div>
                <div>
                    <h1 class="registros-medicos-title">Registros médicos</h1>
                    <p class="registros-medicos-subtitle">Consulta clínica de las atenciones realizadas.</p>
                </div>
            </div>

            @if($puedeCrear)
                <button type="button"
                        class="btn btn-primary registros-medicos-create-button"
                        data-bs-toggle="modal"
                        data-bs-target="#modalNuevo">
                    <span class="material-symbols-outlined">note_add</span>
                    Nuevo registro médico
                </button>
            @endif
        </header>


        {{-- SUPERFICIE --}}
        <section class="registros-medicos-surface">

            <div class="registros-medicos-surface-header">
                <div>
                    <h2 class="registros-medicos-surface-title">Historial de atenciones</h2>
                    <p class="registros-medicos-surface-description">Registros clínicos, ordenados por fecha.</p>
                </div>
                <div class="registros-medicos-record-indicator">
                    <span class="material-symbols-outlined">database</span>
                    <span>{{ $lista->count() }} registros</span>
                </div>
            </div>

            @if($lista->isEmpty())
                <div class="registros-medicos-empty">
                    <span class="material-symbols-outlined">clinical_notes</span>
                    <h3>Aún no hay registros</h3>
                    <p>Cuando existan datos aparecerán aquí.</p>
                </div>
            @else
            <div class="registros-medicos-toolbar">
                <select class="form-select" data-filtro-columna="3" aria-label="Filtrar por doctor">
                    <option value="">Doctor: todos</option>
                </select>
                <button type="button" class="btn btn-text registros-medicos-toolbar-clear" data-filtros-limpiar>Limpiar filtros</button>
            </div>

            <div class="table-responsive registros-medicos-table-container">
                <table id="tabla-registros-medicos" class="table registros-medicos-table align-middle mb-0" data-page-length="10">
                    <thead>
                        <tr>
                            <th scope="col" class="registros-medicos-column-id">ID</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Paciente</th>
                            <th scope="col">Doctor</th>
                            <th scope="col">Motivo</th>
                            <th scope="col">Observaciones</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($lista as $item)
                        <tr>
                            <td class="registros-medicos-id">#{{ $item->id }}</td>
                            <td>{{ $fmtFecha($item->fecha_registro, 'd/m/Y H:i') }}</td>
                            <td>
                              <div class="registros-medicos-identity">
                                <div class="registros-medicos-avatar"><span class="material-symbols-outlined">person</span></div>
                                <div class="registros-medicos-identity-data">
                                  <span class="registros-medicos-fullname" data-filtro-valor>{{ ($item->paciente?->nombres ?? '') . ' ' . ($item->paciente?->apellidos ?? '') }}</span>
                                </div>
                              </div>
                            </td>
                            <td>{{ ($item->doctor?->usuario?->nombres ?? '') . ' ' . ($item->doctor?->usuario?->apellidos ?? '') ?? "—" }}</td>
                            <td class="registros-medicos-muted" title="{{ $item->motivo_consulta }}">{{ \Illuminate\Support\Str::limit((string) ($item->motivo_consulta ?? ""), 70) ?: "—" }}</td>
                            <td class="registros-medicos-muted" title="{{ $item->observaciones }}">{{ \Illuminate\Support\Str::limit((string) ($item->observaciones ?? ""), 70) ?: "—" }}</td>
                            <td>
                                <div class="registros-medicos-actions">
                                    <button type="button" class="registros-medicos-action-button"
                                            data-bs-toggle="modal" data-bs-target="#modalVer"
                                            data-id="{{ $item->id }}"
                                            title="Consultar registro médico" aria-label="Consultar registro médico">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </button>
                                    @if($puedeEliminar)
                                        <button type="button" class="registros-medicos-action-button registros-medicos-action-delete"
                                                data-bs-toggle="modal" data-bs-target="#modalEliminar"
                                                data-id="{{ $item->id }}"
                                                data-nombre="{{ ($item->paciente?->nombres ?? "") . " " . ($item->paciente?->apellidos ?? "") }}"
                                                data-sub="{{ $fmtFecha($item->fecha_registro, "d/m/Y H:i") }}"
                                                title="Eliminar registro médico" aria-label="Eliminar registro médico">
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
    <div class="modal fade registros-medicos-modal" id="modalNuevo" tabindex="-1" aria-labelledby="tituloNuevo" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="registros-medicos-modal-header">
                    <div class="registros-medicos-modal-heading">
                        <div class="registros-medicos-modal-icon"><span class="material-symbols-outlined">note_add</span></div>
                        <div>
                            <h2 class="registros-medicos-modal-title" id="tituloNuevo">Registrar registro médico</h2>
                            <p class="registros-medicos-modal-subtitle">Completa la información solicitada.</p>
                        </div>
                    </div>
                    <button type="button" class="registros-medicos-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="formNuevo" method="POST" action="{{ route('registros_medicos.insertar') }}" novalidate>
                    @csrf
                    <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                    <div class="modal-body registros-medicos-modal-body">
                        <div class="registros-medicos-form-section">
                          <div class="registros-medicos-form-section-heading">
                            <span class="material-symbols-outlined">event_note</span>
                            <div>
                              <h3>Atención</h3>
                              <p>Paciente, doctor y cita asociada.</p>
                            </div>
                          </div>
                          <div class="row g-3">
                            <div class="col-md-6">
                              <div class="form-floating">
                                <select name="id_paciente" id="nuevo_id_paciente" class="form-select" required>
                                  <option value="" disabled selected></option>
                                  @foreach(($pacientes ?? collect()) as $o)
                                    <option value="{{ $o->id }}">{{ ($o->nombres ?? '') . ' ' . ($o->apellidos ?? '') . ' · ' . ($o->numero_identificacion ?? '') }}</option>
                                  @endforeach
                                </select>
                                <label for="nuevo_id_paciente">Paciente</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-floating">
                                <select name="id_doctor" id="nuevo_id_doctor" class="form-select" required>
                                  <option value="" disabled selected></option>
                                  @foreach(($doctores ?? collect()) as $o)
                                    <option value="{{ $o->id }}">{{ ($o->usuario?->nombres ?? '') . ' ' . ($o->usuario?->apellidos ?? '') }}</option>
                                  @endforeach
                                </select>
                                <label for="nuevo_id_doctor">Doctor</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-floating">
                                <select name="id_cita" id="nuevo_id_cita" class="form-select">
                                  <option value="" selected>Sin especificar</option>
                                  @foreach(($citas ?? collect()) as $o)
                                    <option value="{{ $o->id }}">{{ '#' . $o->id . ' · ' . ($o->fecha_cita ? $o->fecha_cita->format('d/m/Y') : '') . ' · ' . ($o->paciente?->nombres ?? '') }}</option>
                                  @endforeach
                                </select>
                                <label for="nuevo_id_cita">Cita asociada</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-floating">
                                <input type="datetime-local" name="fecha_registro" id="nuevo_fecha_registro" class="form-control" placeholder="Fecha del registro">
                                <label for="nuevo_fecha_registro">Fecha del registro</label>
                              </div>
                              <div class="registros-medicos-field-hint">Déjalo en blanco para usar la fecha actual.</div>
                            </div>
                          </div>
                        </div>
                        <div class="registros-medicos-form-section">
                          <div class="registros-medicos-form-section-heading">
                            <span class="material-symbols-outlined">clinical_notes</span>
                            <div>
                              <h3>Consulta</h3>
                              <p>Motivo, notas clínicas y observaciones.</p>
                            </div>
                          </div>
                          <div class="row g-3">
                            <div class="col-md-12">
                              <div class="form-floating">
                                <textarea name="motivo_consulta" id="nuevo_motivo_consulta" class="form-control" placeholder="Motivo de consulta"></textarea>
                                <label for="nuevo_motivo_consulta">Motivo de consulta</label>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="form-floating">
                                <textarea name="notas_clinicas" id="nuevo_notas_clinicas" class="form-control" placeholder="Notas clínicas"></textarea>
                                <label for="nuevo_notas_clinicas">Notas clínicas</label>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="form-floating">
                                <textarea name="observaciones" id="nuevo_observaciones" class="form-control" placeholder="Observaciones"></textarea>
                                <label for="nuevo_observaciones">Observaciones</label>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="registros-medicos-form-section">
                          <div class="registros-medicos-form-section-heading">
                            <span class="material-symbols-outlined">monitor_heart</span>
                            <div>
                              <h3>Signos vitales</h3>
                              <p>Opcional. Se guardan junto con el registro.</p>
                            </div>
                          </div>
                          <div class="row g-3">
                            <div class="col-md-3">
                              <div class="form-floating">
                                <input type="number" name="signos_vitales[peso]" id="nuevo_signos_vitales_peso_" class="form-control" placeholder="Peso">
                                <label for="nuevo_signos_vitales_peso_">Peso</label>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-floating">
                                <input type="number" name="signos_vitales[altura]" id="nuevo_signos_vitales_altura_" class="form-control" placeholder="Altura">
                                <label for="nuevo_signos_vitales_altura_">Altura</label>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-floating">
                                <input type="number" name="signos_vitales[temperatura]" id="nuevo_signos_vitales_temperatura_" class="form-control" placeholder="Temperatura">
                                <label for="nuevo_signos_vitales_temperatura_">Temperatura</label>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-floating">
                                <input type="number" name="signos_vitales[saturacion_oxigeno]" id="nuevo_signos_vitales_saturacion_oxigeno_" class="form-control" placeholder="Saturación O₂">
                                <label for="nuevo_signos_vitales_saturacion_oxigeno_">Saturación O₂</label>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-floating">
                                <input type="number" name="signos_vitales[frecuencia_cardiaca]" id="nuevo_signos_vitales_frecuencia_cardiaca_" class="form-control" placeholder="Frec. cardíaca">
                                <label for="nuevo_signos_vitales_frecuencia_cardiaca_">Frec. cardíaca</label>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-floating">
                                <input type="number" name="signos_vitales[frecuencia_respiratoria]" id="nuevo_signos_vitales_frecuencia_respiratoria_" class="form-control" placeholder="Frec. respiratoria">
                                <label for="nuevo_signos_vitales_frecuencia_respiratoria_">Frec. respiratoria</label>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-floating">
                                <input type="number" name="signos_vitales[presion_sistolica]" id="nuevo_signos_vitales_presion_sistolica_" class="form-control" placeholder="Presión sistólica">
                                <label for="nuevo_signos_vitales_presion_sistolica_">Presión sistólica</label>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-floating">
                                <input type="number" name="signos_vitales[presion_diastolica]" id="nuevo_signos_vitales_presion_diastolica_" class="form-control" placeholder="Presión diastólica">
                                <label for="nuevo_signos_vitales_presion_diastolica_">Presión diastólica</label>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>

                    <div class="registros-medicos-modal-footer">
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

    {{-- MODAL: CONSULTA --}}
    <div class="modal fade registros-medicos-modal" id="modalVer" tabindex="-1" aria-labelledby="tituloVer" aria-hidden="true"
         data-buscar-url="{{ route('registros_medicos.buscar', ['id' => '__ID__']) }}">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="registros-medicos-modal-header">
                    <div class="registros-medicos-modal-heading">
                        <div class="registros-medicos-modal-icon"><span class="material-symbols-outlined">visibility</span></div>
                        <div>
                            <h2 class="registros-medicos-modal-title" id="tituloVer">Detalle de registro médico</h2>
                            <p class="registros-medicos-modal-subtitle">Información completa del registro.</p>
                        </div>
                    </div>
                    <button type="button" class="registros-medicos-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                <div class="modal-body registros-medicos-modal-body">
                    <dl class="registros-medicos-detail-grid">
                        <div class="registros-medicos-detail-item"><dt>Paciente</dt><dd data-ver="paciente.nombres+paciente.apellidos">—</dd></div>
                        <div class="registros-medicos-detail-item"><dt>Doctor</dt><dd data-ver="doctor.usuario.nombres+doctor.usuario.apellidos">—</dd></div>
                        <div class="registros-medicos-detail-item"><dt>Fecha del registro</dt><dd data-ver="fecha_registro" data-formato="fechahora">—</dd></div>
                        <div class="registros-medicos-detail-item"><dt>Cita asociada</dt><dd data-ver="cita.fecha_cita" data-formato="fecha">—</dd></div>
                        <div class="registros-medicos-detail-item is-wide"><dt>Motivo de consulta</dt><dd data-ver="motivo_consulta">—</dd></div>
                        <div class="registros-medicos-detail-item is-wide"><dt>Notas clínicas</dt><dd data-ver="notas_clinicas">—</dd></div>
                        <div class="registros-medicos-detail-item is-wide"><dt>Observaciones</dt><dd data-ver="observaciones">—</dd></div>
                        <div class="registros-medicos-detail-item"><dt>Peso</dt><dd data-ver="signos_vitales.peso">—</dd></div>
                        <div class="registros-medicos-detail-item"><dt>Altura</dt><dd data-ver="signos_vitales.altura">—</dd></div>
                        <div class="registros-medicos-detail-item"><dt>Temperatura</dt><dd data-ver="signos_vitales.temperatura">—</dd></div>
                        <div class="registros-medicos-detail-item"><dt>Saturación O₂</dt><dd data-ver="signos_vitales.saturacion_oxigeno">—</dd></div>
                        <div class="registros-medicos-detail-item"><dt>Frecuencia cardíaca</dt><dd data-ver="signos_vitales.frecuencia_cardiaca">—</dd></div>
                        <div class="registros-medicos-detail-item"><dt>Frecuencia respiratoria</dt><dd data-ver="signos_vitales.frecuencia_respiratoria">—</dd></div>
                        <div class="registros-medicos-detail-item"><dt>Presión</dt><dd data-ver="signos_vitales.presion_sistolica+signos_vitales.presion_diastolica">—</dd></div>
                    </dl>
                </div>

                <div class="registros-medicos-modal-footer">
                    <button type="button" class="btn btn-tonal" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: ELIMINAR --}}
    @if($puedeEliminar)
    <div class="modal fade registros-medicos-modal registros-medicos-delete-modal" id="modalEliminar" tabindex="-1" aria-labelledby="tituloEliminar" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="registros-medicos-delete-content">
                    <div class="registros-medicos-delete-icon"><span class="material-symbols-outlined">delete</span></div>

                    <button type="button" class="registros-medicos-modal-close registros-medicos-delete-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span class="material-symbols-outlined">close</span>
                    </button>

                    <div class="registros-medicos-delete-heading">
                        <span class="registros-medicos-delete-overline">Confirmación requerida</span>
                        <h2 id="tituloEliminar">¿Eliminar este registro médico?</h2>
                        <p>Esta acción no se puede deshacer desde la interfaz.</p>
                    </div>

                    <form id="formEliminar" method="POST"
                          data-url="{{ route('registros_medicos.eliminar', ['registroMedico' => '__ID__']) }}" novalidate>
                        @csrf
                        @method('DELETE')
                        <div class="alert alert-danger d-none" role="alert" data-form-errors></div>

                        <div class="registros-medicos-delete-identity">
                            <div class="registros-medicos-delete-avatar"><span class="material-symbols-outlined">person</span></div>
                            <div class="registros-medicos-delete-data">
                                <span class="registros-medicos-delete-name" id="eliminar_nombre">—</span>
                                <span class="registros-medicos-delete-sub" id="eliminar_sub">—</span>
                                <span class="registros-medicos-delete-id">Identificador: #<span id="eliminar_id">—</span></span>
                            </div>
                        </div>

                        <div class="registros-medicos-delete-notice">
                            <span class="material-symbols-outlined">info</span>
                            <p>Los datos clínicos requieren trazabilidad. Confirma que la eliminación es correcta.</p>
                        </div>

                        <div class="registros-medicos-delete-actions">
                            <button type="button" class="btn btn-tonal" data-bs-dismiss="modal">Conservar</button>
                            <button type="submit" class="btn registros-medicos-delete-confirm">
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
        VISTA: Administración y consulta de registros médicos.

        OBJETIVO:
        Proporcionar una interfaz para consultar los registros clínicos
        y acceder al expediente correspondiente.

        CONTENIDO:
        1. ENCABEZADO:
           - Título "Registros médicos".
           - Descripción del módulo.

        2. FILTROS:
           - Paciente.
           - Doctor responsable.
           - Fecha o rango de fechas.
           - Diagnóstico asociado, si resulta útil.

        3. TABLA:
           - Identificador del registro.
           - Paciente.
           - Doctor responsable.
           - Fecha de atención.
           - Motivo de consulta.
           - Resumen u observación.
           - Acciones.

        4. FORMULARIO / MODAL:
           - Seleccionar paciente.
           - Seleccionar doctor, cuando corresponda.
           - Asociar cita, si existe.
           - Registrar motivo de consulta.
           - Registrar observaciones y evolución.
           - Guardar el registro.

        5. ACCIONES:
           - Consultar registro.
           - Acceder al expediente clínico.
           - Consultar diagnósticos asociados.
           - Eliminar o anular según las reglas del sistema.

        CONSIDERACIONES:
        - Los diagnósticos asociados se administran mediante la relación
          registro_medico_diagnosticos.
        - No duplicar la gestión de diagnósticos en esta pantalla.
        - El expediente del paciente será la interfaz principal para
          consultar su historial clínico.
        - Proteger los datos clínicos y mantener trazabilidad de operaciones.
        - No asumir que todos los usuarios pueden crear o eliminar registros.
    --}}
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('js/registro_medico/registroMedico.js') }}"></script>
@endpush
