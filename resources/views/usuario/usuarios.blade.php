@extends('layouts.app_layout')

@section('titulo', 'Usuarios')

@push('estilos')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('css/usuarios.css') }}">
@endpush

@section('contenido')
    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0 fw-bold page-title d-flex align-items-center gap-2">
            <span class="material-symbols-outlined fs-2 page-title-icon">group</span>
            Usuarios del sistema
        </h1>

        <button type="button"
                class="btn btn-brand rounded-pill fw-medium px-4 shadow-sm d-flex align-items-center gap-2"
                data-bs-toggle="modal"
                data-bs-target="#modalNuevoUsuario">
            <span class="material-symbols-outlined fs-5">add</span>
            Nuevo usuario
        </button>
    </div>

    {{-- TABLA DE USUARIOS --}}
    <div class="card md3-card-table shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive p-3 p-md-4">
                <table id="tbl" class="table md3-table align-middle mb-0">
                    <thead class="md3-table-header">
                    <tr>
                        <th class="border-0">#</th>
                        <th class="border-0">Nombres</th>
                        <th class="border-0">Apellidos</th>
                        <th class="border-0">Correo</th>
                        <th class="border-0">Rol</th>
                        <th class="border-0">Estado</th>
                        <th class="border-0 text-center">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($usuarios as $usuario)
                        <tr>
                            <td class="opacity-75 fw-medium">{{ $usuario->id }}</td>
                            <td class="fw-medium">{{ $usuario->nombres }}</td>
                            <td class="fw-medium">{{ $usuario->apellidos }}</td>
                            <td>{{ $usuario->correo }}</td>
                            <td><span class="md3-badge md3-badge-secondary">{{ $usuario->rol?->rol ?? 'N/A' }}</span></td>
                            <td><span class="md3-badge md3-badge-primary">{{ $usuario->estado?->estado ?? 'N/A' }}</span></td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">

                                    @if ($usuario->id_estado !== \App\Models\Usuario::ESTADO_ELIMINADO)

                                        {{-- EDITAR --}}
                                        <button type="button"
                                                class="btn-icon-soft btn-icon-soft-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalModificarUsuario"
                                                data-id="{{ $usuario->id }}"
                                                title="Modificar usuario">

                                        <span class="material-symbols-outlined fs-5">
                                            edit
                                        </span>

                                        </button>

                                        {{-- ELIMINAR --}}
                                        <button type="button"
                                                class="btn-icon-soft btn-icon-soft-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEliminarUsuario"
                                                data-id="{{ $usuario->id }}"
                                                data-nombre="{{ $usuario->nombres }} {{ $usuario->apellidos }}"
                                                data-correo="{{ $usuario->correo }}"
                                                title="Eliminar usuario">

                                                <span class="material-symbols-outlined fs-5">
                                                    delete
                                                </span>

                                        </button>

                                    @else
                                        <span class="small text-muted d-flex align-items-center gap-1">
                                            <span class="material-symbols-outlined fs-6">
                                                person_off
                                            </span>
                                            Usuario eliminado
                                        </span>

                                    @endif

                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL: NUEVO USUARIO --}}
    <div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-labelledby="tituloNuevoUsuario" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content md3-modal-content shadow-lg">
                <div class="modal-header md3-modal-header align-items-center">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="tituloNuevoUsuario">
                        <span class="material-symbols-outlined text-primary fs-3">person_add</span>
                        Registrar usuario
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form method="POST" action="{{ route('usuarios.insertar') }}">
                    @csrf
                    <div class="modal-body md3-modal-body mt-2">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select name="id_rol" id="nuevo_id_rol" class="form-select md3-form-select" required>
                                        <option value="" disabled selected>Seleccione</option>
                                        @foreach($roles as $rol)
                                            <option value="{{ $rol->id }}" {{ old('id_rol') == $rol->id ? 'selected' : '' }}>{{ $rol->rol }}</option>
                                        @endforeach
                                    </select>
                                    <label for="nuevo_id_rol">Rol del sistema</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select name="id_estado" id="nuevo_id_estado" class="form-select md3-form-select" required>
                                        <option value="" disabled selected>Seleccione</option>
                                        @foreach($estados as $estado)
                                            <option value="{{ $estado->id }}" {{ old('id_estado') == $estado->id ? 'selected' : '' }}>{{ $estado->estado }}</option>
                                        @endforeach
                                    </select>
                                    <label for="nuevo_id_estado">Estado</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="nombres" id="nuevo_nombres" class="form-control md3-form-control" placeholder="Nombres" maxlength="100" value="{{ old('nombres') }}" required>
                                    <label for="nuevo_nombres">Nombres</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="apellidos" id="nuevo_apellidos" class="form-control md3-form-control" placeholder="Apellidos" maxlength="100" value="{{ old('apellidos') }}" required>
                                    <label for="nuevo_apellidos">Apellidos</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" name="correo" id="nuevo_correo" class="form-control md3-form-control" placeholder="Correo electrónico" maxlength="150" value="{{ old('correo') }}" required>
                                    <label for="nuevo_correo">Correo electrónico</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" name="telefono" id="nuevo_telefono" class="form-control md3-form-control" placeholder="Teléfono" maxlength="25" value="{{ old('telefono') }}">
                                    <label for="nuevo_telefono">Teléfono</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="password" name="contrasenha" id="nuevo_contrasenha" class="form-control md3-form-control" placeholder="Contraseña" maxlength="250" autocomplete="new-password" required>
                                    <label for="nuevo_contrasenha">Contraseña</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="password" name="contrasenha_confirmation" id="nuevo_contrasenha_confirmation" class="form-control md3-form-control" placeholder="Confirmar contraseña" maxlength="250" autocomplete="new-password" required>
                                    <label for="nuevo_contrasenha_confirmation">Confirmar contraseña</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer md3-modal-footer">
                        <button type="button" class="btn btn-soft-cancel" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-brand rounded-pill px-4 fw-medium shadow-sm d-flex align-items-center gap-1">
                            <span class="material-symbols-outlined fs-5">check</span> Guardar usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- =========================================================
     MODAL ÚNICO: MODIFICAR USUARIO
========================================================== --}}
    <div class="modal fade"
         id="modalModificarUsuario"
         tabindex="-1"
         aria-labelledby="tituloModificarUsuario"
         aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content md3-modal-content shadow-lg">

                <div class="modal-header md3-modal-header align-items-center">

                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2"
                        id="tituloModificarUsuario">

                    <span class="material-symbols-outlined text-primary fs-3">
                        edit
                    </span>

                        Modificar usuario
                    </h5>

                    <button type="button"
                            class="btn-close shadow-none"
                            data-bs-dismiss="modal"
                            aria-label="Cerrar">
                    </button>

                </div>


                {{-- FORMULARIO --}}
                <form method="POST"
                      id="formEditarUsuario"
                      data-url="{{ route('usuarios.actualizar', ['usuario' => '__ID__']) }}">

                    @csrf
                    @method('PUT')

                    <div class="modal-body md3-modal-body mt-2">

                        <div class="row g-3">

                            {{-- ROL --}}
                            <div class="col-md-6">

                                <div class="form-floating">

                                    <select name="id_rol"
                                            id="editar_rol"
                                            class="form-select md3-form-select"
                                            required>

                                        @foreach($roles as $rol)

                                            <option value="{{ $rol->id }}">
                                                {{ $rol->rol }}
                                            </option>

                                        @endforeach

                                    </select>

                                    <label for="editar_rol">
                                        Rol del sistema
                                    </label>

                                </div>

                            </div>


                            {{-- ESTADO --}}
                            <div class="col-md-6">

                                <div class="form-floating">

                                    <select name="id_estado"
                                            id="editar_estado"
                                            class="form-select md3-form-select"
                                            required>

                                        @foreach($estados as $estado)

                                            <option value="{{ $estado->id }}">
                                                {{ $estado->estado }}
                                            </option>

                                        @endforeach

                                    </select>

                                    <label for="editar_estado">
                                        Estado
                                    </label>

                                </div>

                            </div>


                            {{-- NOMBRES --}}
                            <div class="col-md-6">

                                <div class="form-floating">

                                    <input type="text"
                                           name="nombres"
                                           id="editar_nombres"
                                           class="form-control md3-form-control"
                                           placeholder="Nombres"
                                           maxlength="100"
                                           required>

                                    <label for="editar_nombres">
                                        Nombres
                                    </label>

                                </div>

                            </div>


                            {{-- APELLIDOS --}}
                            <div class="col-md-6">

                                <div class="form-floating">

                                    <input type="text"
                                           name="apellidos"
                                           id="editar_apellidos"
                                           class="form-control md3-form-control"
                                           placeholder="Apellidos"
                                           maxlength="100"
                                           required>

                                    <label for="editar_apellidos">
                                        Apellidos
                                    </label>

                                </div>

                            </div>


                            {{-- CORREO --}}
                            <div class="col-md-6">

                                <div class="form-floating">

                                    <input type="email"
                                           name="correo"
                                           id="editar_correo"
                                           class="form-control md3-form-control"
                                           placeholder="Correo"
                                           maxlength="150"
                                           required>

                                    <label for="editar_correo">
                                        Correo electrónico
                                    </label>

                                </div>

                            </div>


                            {{-- TELÉFONO --}}
                            <div class="col-md-6">

                                <div class="form-floating">

                                    <input type="tel"
                                           name="telefono"
                                           id="editar_telefono"
                                           class="form-control md3-form-control"
                                           placeholder="Teléfono"
                                           maxlength="25">

                                    <label for="editar_telefono">
                                        Teléfono
                                    </label>

                                </div>

                            </div>


                            {{-- CONTRASEÑA --}}
                            <div class="col-md-6">

                                <div class="form-floating">

                                    <input type="password"
                                           name="contrasenha"
                                           id="editar_contrasenha"
                                           class="form-control md3-form-control"
                                           placeholder="Nueva contraseña"
                                           maxlength="250"
                                           autocomplete="new-password">

                                    <label for="editar_contrasenha">
                                        Nueva contraseña
                                    </label>

                                </div>

                                <div class="form-text opacity-75 small mt-1 ms-2">
                                    Déjalo en blanco para conservarla.
                                </div>

                            </div>


                            {{-- CONFIRMACIÓN --}}
                            <div class="col-md-6">

                                <div class="form-floating">

                                    <input type="password"
                                           name="contrasenha_confirmation"
                                           id="editar_confirmacion"
                                           class="form-control md3-form-control"
                                           placeholder="Confirmar contraseña"
                                           maxlength="250"
                                           autocomplete="new-password">

                                    <label for="editar_confirmacion">
                                        Confirmar contraseña
                                    </label>

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="modal-footer md3-modal-footer">

                        <button type="button"
                                class="btn btn-soft-cancel"
                                data-bs-dismiss="modal">

                            Cancelar

                        </button>

                        <button type="submit"
                                class="btn btn-brand rounded-pill px-4 fw-medium shadow-sm d-flex align-items-center gap-1">

                        <span class="material-symbols-outlined fs-5">
                            check
                        </span>

                            Guardar cambios

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

    {{-- =========================================================
     MODAL ÚNICO: ELIMINAR USUARIO
========================================================== --}}
    <div class="modal fade"
         id="modalEliminarUsuario"
         tabindex="-1"
         aria-labelledby="tituloEliminarUsuario"
         aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content md3-modal-content shadow-lg">

                <div class="modal-header md3-modal-header align-items-center"
                     style="background: var(--md-sys-color-surface-container);">

                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2 text-danger"
                        id="tituloEliminarUsuario">

                    <span class="material-symbols-outlined fs-3">
                        warning
                    </span>

                        Eliminar usuario

                    </h5>

                    <button type="button"
                            class="btn-close shadow-none"
                            data-bs-dismiss="modal"
                            aria-label="Cerrar">
                    </button>

                </div>


                <form method="POST"
                      id="formEliminarUsuario"
                      data-url="{{ route('usuarios.eliminar', ['usuario' => '__ID__']) }}">

                    @csrf
                    @method('DELETE')

                    <div class="modal-body md3-modal-body mt-2">

                        <p class="mb-3">
                            ¿Estás seguro de que deseas eliminar este usuario?
                        </p>


                        <div class="md3-container-variant mb-3">

                            <div class="d-flex align-items-center gap-3">

                            <span class="material-symbols-outlined fs-1 text-primary">
                                account_circle
                            </span>

                                <div>

                                    <div class="fw-bold"
                                         id="eliminar_nombre">
                                        —
                                    </div>

                                    <div class="small opacity-75"
                                         id="eliminar_correo">
                                        —
                                    </div>

                                    <div class="small opacity-50 mt-1">
                                        ID:
                                        <span id="eliminar_id">—</span>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="modal-footer md3-modal-footer">

                        <button type="button"
                                class="btn btn-soft-cancel"
                                data-bs-dismiss="modal">

                            Cancelar

                        </button>

                        <button type="submit"
                                class="btn btn-danger rounded-pill px-4 fw-medium shadow-sm d-flex align-items-center gap-1">

                        <span class="material-symbols-outlined fs-5">
                            delete
                        </span>

                            Eliminar

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('js/dataTable.js') }}"></script>

    <script>

        /*
        |--------------------------------------------------------------------------
        | EDITAR USUARIO
        |--------------------------------------------------------------------------
        */

        function editarUsuario(id)
        {
            const formulario = $('#formEditarUsuario');

            const url = @json(
                route('usuarios.buscar', ['id' => '__ID__'])
            );

            const urlBuscar = url.replace('__ID__', id);

            $.get(urlBuscar)
                .done(function(usuario)
                {
                    formulario[0].reset();

                    $('#editar_rol').val(usuario.id_rol);
                    $('#editar_estado').val(usuario.id_estado);
                    $('#editar_nombres').val(usuario.nombres);
                    $('#editar_apellidos').val(usuario.apellidos);
                    $('#editar_correo').val(usuario.correo);
                    $('#editar_telefono').val(
                        usuario.telefono ?? ''
                    );

                    $('#editar_contrasenha').val('');
                    $('#editar_confirmacion').val('');

                    $('#tituloModificarUsuario').html(`
                        <span class="material-symbols-outlined text-primary fs-3">
                            edit
                        </span>
                        Modificar usuario #${usuario.id}
                    `);

                    formulario.attr(
                        'data-id',
                        usuario.id
                    );
                })
                .fail(function()
                {
                    alert('Error al consultar el usuario.');
                });
        }


        $('#modalModificarUsuario').on(
            'show.bs.modal',
            function(event)
            {
                const boton = $(event.relatedTarget);

                const id = boton.data('id');

                editarUsuario(id);
            }
        );


        /*
        |--------------------------------------------------------------------------
        | SUBMIT: ACTUALIZAR USUARIO
        |--------------------------------------------------------------------------
        */

        $('#formEditarUsuario').on(
            'submit',
            function()
            {
                const formulario = $(this);

                const id = formulario.attr('data-id');

                const url = formulario.data('url');

                const urlFinal = url.replace(
                    '__ID__',
                    id
                );

                formulario.attr(
                    'action',
                    urlFinal
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | ELIMINAR USUARIO
        |--------------------------------------------------------------------------
        */

        function eliminarUsuario(
            id,
            nombre,
            correo
        )
        {
            $('#eliminar_id').text(id);
            $('#eliminar_nombre').text(nombre);
            $('#eliminar_correo').text(correo);

            $('#formEliminarUsuario').attr(
                'data-id',
                id
            );
        }


        $('#modalEliminarUsuario').on(
            'show.bs.modal',
            function(event)
            {
                const boton = $(event.relatedTarget);

                const id = boton.data('id');
                const nombre = boton.data('nombre');
                const correo = boton.data('correo');

                eliminarUsuario(
                    id,
                    nombre,
                    correo
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | SUBMIT: ELIMINAR USUARIO
        |--------------------------------------------------------------------------
        */

        $('#formEliminarUsuario').on(
            'submit',
            function()
            {
                const formulario = $(this);

                const id = formulario.attr('data-id');

                const url = formulario.data('url');

                const urlFinal = url.replace(
                    '__ID__',
                    id
                );

                formulario.attr(
                    'action',
                    urlFinal
                );
            }
        );

    </script>

@endpush
