@extends('layouts.app_layout')

@section('titulo', 'Usuarios')

@push('estilos')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('css/usuarios.css') }}">
@endpush

@section('contenido')

    <div class="usuarios-page">

        {{-- ENCABEZADO --}}
        <header class="usuarios-header">
            <div class="usuarios-heading">
                <div class="usuarios-heading-icon">
                    <span class="material-symbols-outlined">group</span>
                </div>

                <div>
                    <h1 class="usuarios-title">Usuarios del sistema</h1>
                    <p class="usuarios-subtitle">
                        Administración de cuentas y permisos de acceso.
                    </p>
                </div>
            </div>

            <button type="button"
                    class="btn btn-primary usuarios-create-button"
                    data-bs-toggle="modal"
                    data-bs-target="#modalNuevoUsuario">

                <span class="material-symbols-outlined">person_add</span>
                Nuevo usuario
            </button>
        </header>


        {{-- SUPERFICIE DE USUARIOS --}}
        <section class="usuarios-surface">

            <div class="usuarios-surface-header">
                <div>
                    <h2 class="usuarios-surface-title">Directorio de usuarios</h2>
                    <p class="usuarios-surface-description">
                        Consulta y administra las cuentas registradas en HealthWare.
                    </p>
                </div>

                <div class="usuarios-record-indicator">
                    <span class="material-symbols-outlined">database</span>
                    <span>{{ $usuarios->count() }} registros</span>
                </div>
            </div>


            {{-- TABLA --}}
            <div class="table-responsive usuarios-table-container">

                <table id="tbl"
                       class="table usuarios-table align-middle mb-0"
                       data-page-length="10">

                    <thead>
                    <tr>
                        <th scope="col" class="usuarios-column-id">ID</th>
                        <th scope="col">Usuario</th>
                        <th scope="col">Correo electrónico</th>
                        <th scope="col">Rol</th>
                        <th scope="col">Estado</th>
                        <th scope="col" class="text-center">Acciones</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($usuarios as $usuario)

                        <tr>
                            {{-- IDENTIFICADOR --}}
                            <td class="usuarios-id">
                                #{{ $usuario->id }}
                            </td>


                            {{-- IDENTIDAD --}}
                            <td>
                                <div class="usuarios-identity">
                                    <div class="usuarios-avatar">
                                            <span class="material-symbols-outlined">
                                                person
                                            </span>
                                    </div>

                                    <div class="usuarios-identity-data">
                                            <span class="usuarios-fullname">
                                                {{ $usuario->nombres }}
                                                {{ $usuario->apellidos }}
                                            </span>

                                        <span class="usuarios-identity-caption">
                                                Cuenta de HealthWare
                                            </span>
                                    </div>
                                </div>
                            </td>


                            {{-- CORREO --}}
                            <td class="usuarios-email">
                                {{ $usuario->correo }}
                            </td>


                            {{-- ROL --}}
                            <td>
                                    <span class="usuarios-role">
                                        {{ $usuario->rol?->rol ?? 'N/A' }}
                                    </span>
                            </td>


                            {{-- ESTADO --}}
                            <td>
                                @if($usuario->id_estado === 1)
                                    <span class="usuarios-status usuarios-status-active">
                                            <span class="usuarios-status-dot"></span>
                                            {{ $usuario->estado?->estado ?? 'Activo' }}
                                        </span>

                                @elseif($usuario->id_estado === 2)
                                    <span class="usuarios-status usuarios-status-inactive">
                                            <span class="usuarios-status-dot"></span>
                                            {{ $usuario->estado?->estado ?? 'Inactivo' }}
                                        </span>

                                @else
                                    <span class="usuarios-status usuarios-status-deleted">
                                            <span class="material-symbols-outlined">
                                                person_off
                                            </span>
                                            {{ $usuario->estado?->estado ?? 'Eliminado' }}
                                        </span>
                                @endif
                            </td>


                            {{-- ACCIONES --}}
                            <td>
                                <div class="usuarios-actions">

                                    @if($usuario->id_estado !== \App\Models\Usuario::ESTADO_ELIMINADO)

                                        {{-- EDITAR --}}
                                        <button type="button"
                                                class="usuarios-action-button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalModificarUsuario"
                                                data-id="{{ $usuario->id }}"
                                                title="Editar usuario"
                                                aria-label="Editar usuario">

                                                <span class="material-symbols-outlined">
                                                    edit
                                                </span>
                                        </button>


                                        {{-- ELIMINAR --}}
                                        <button type="button"
                                                class="usuarios-action-button usuarios-action-delete"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEliminarUsuario"
                                                data-id="{{ $usuario->id }}"
                                                data-nombre="{{ $usuario->nombres }} {{ $usuario->apellidos }}"
                                                data-correo="{{ $usuario->correo }}"
                                                title="Eliminar usuario"
                                                aria-label="Eliminar usuario">

                                                <span class="material-symbols-outlined">
                                                    delete
                                                </span>
                                        </button>

                                    @else

                                        <span class="usuarios-deleted-label">
                                                <span class="material-symbols-outlined">
                                                    lock
                                                </span>
                                                Registro eliminado
                                            </span>

                                    @endif

                                </div>
                            </td>

                        </tr>

                    @endforeach
                    </tbody>

                </table>

            </div>

        </section>

    </div>


    {{-- =========================================================
         MODAL: NUEVO USUARIO
    ========================================================== --}}
    <div class="modal fade usuarios-modal"
         id="modalNuevoUsuario"
         tabindex="-1"
         aria-labelledby="tituloNuevoUsuario"
         aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">

            <div class="modal-content">

                <div class="usuarios-modal-header">

                    <div class="usuarios-modal-heading">
                        <div class="usuarios-modal-icon">
                            <span class="material-symbols-outlined">person_add</span>
                        </div>

                        <div>
                            <h2 class="usuarios-modal-title" id="tituloNuevoUsuario">
                                Registrar usuario
                            </h2>

                            <p class="usuarios-modal-subtitle">
                                Crea una cuenta para acceder al sistema.
                            </p>
                        </div>
                    </div>

                    <button type="button"
                            class="usuarios-modal-close"
                            data-bs-dismiss="modal"
                            aria-label="Cerrar">

                        <span class="material-symbols-outlined">close</span>
                    </button>

                </div>


                <form
                    id="formNuevoUsuario"
                    method="POST"
                    action="{{ route('usuarios.insertar') }}"
                    novalidate
                >

                    @csrf

                    <div
                        class="alert alert-danger d-none"
                        role="alert"
                        data-form-errors
                    ></div>

                    <div class="modal-body usuarios-modal-body">

                        {{-- INFORMACIÓN DE LA CUENTA --}}
                        <div class="usuarios-form-section">

                            <div class="usuarios-form-section-heading">
                                <span class="material-symbols-outlined">
                                    manage_accounts
                                </span>

                                <div>
                                    <h3>Información de la cuenta</h3>
                                    <p>Define el rol y el acceso del usuario.</p>
                                </div>
                            </div>


                            <div class="row g-3">

                                {{-- ROL --}}
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="id_rol"
                                                id="nuevo_id_rol"
                                                class="form-select"
                                                required>

                                            <option value="" disabled
                                                {{ old('id_rol') ? '' : 'selected' }}>
                                            </option>

                                            @foreach($roles as $rol)
                                                <option value="{{ $rol->id }}"
                                                    {{ old('id_rol') == $rol->id ? 'selected' : '' }}>
                                                    {{ $rol->rol }}
                                                </option>
                                            @endforeach

                                        </select>

                                        <label for="nuevo_id_rol">Rol del sistema</label>
                                    </div>
                                </div>


                                {{-- ESTADO: SWITCH --}}
                                <div class="col-md-6">

                                    <div class="usuarios-switch-container">

                                        <div class="usuarios-switch-information">
                                            <span class="material-symbols-outlined">
                                                toggle_on
                                            </span>

                                            <div>
                                                <span class="usuarios-switch-title">
                                                    Acceso a la plataforma
                                                </span>

                                                <span class="usuarios-switch-description">
                                                    Activar o desactivar la cuenta.
                                                </span>
                                            </div>
                                        </div>

                                        <div class="form-check form-switch usuarios-switch">
                                            <input type="hidden"
                                                   name="id_estado"
                                                   value="2">

                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   role="switch"
                                                   id="nuevo_id_estado"
                                                   name="id_estado"
                                                   value="1"
                                                {{ old('id_estado', 1) == 1 ? 'checked' : '' }}>

                                            <label class="visually-hidden"
                                                   for="nuevo_id_estado">
                                                Activar usuario
                                            </label>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- INFORMACIÓN PERSONAL --}}
                        <div class="usuarios-form-section">

                            <div class="usuarios-form-section-heading">
                                <span class="material-symbols-outlined">
                                    badge
                                </span>

                                <div>
                                    <h3>Información personal</h3>
                                    <p>Datos de identificación y contacto.</p>
                                </div>
                            </div>


                            <div class="row g-3">

                                {{-- NOMBRES --}}
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text"
                                               name="nombres"
                                               id="nuevo_nombres"
                                               class="form-control"
                                               placeholder="Nombres"
                                               maxlength="100"
                                               value="{{ old('nombres') }}"
                                               required>

                                        <label for="nuevo_nombres">Nombres</label>
                                    </div>
                                </div>


                                {{-- APELLIDOS --}}
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text"
                                               name="apellidos"
                                               id="nuevo_apellidos"
                                               class="form-control"
                                               placeholder="Apellidos"
                                               maxlength="100"
                                               value="{{ old('apellidos') }}"
                                               required>

                                        <label for="nuevo_apellidos">Apellidos</label>
                                    </div>
                                </div>


                                {{-- CORREO --}}
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email"
                                               name="correo"
                                               id="nuevo_correo"
                                               class="form-control"
                                               placeholder="Correo electrónico"
                                               maxlength="150"
                                               value="{{ old('correo') }}"
                                               required>

                                        <label for="nuevo_correo">
                                            Correo electrónico
                                        </label>
                                    </div>
                                </div>


                                {{-- TELÉFONO --}}
                                <div class="col-md-6">

                                    <div class="input-group usuarios-phone-field">
                                        <span class="input-group-text">+503</span>

                                        <input type="tel"
                                               name="telefono"
                                               id="nuevo_telefono"
                                               class="form-control"
                                               placeholder="0000 0000"
                                               maxlength="25"
                                               value="{{ old('telefono') }}"
                                               autocomplete="tel-national">
                                    </div>
                                </div>

                            </div>

                        </div>


                        {{-- SEGURIDAD --}}
                        <div class="usuarios-form-section">

                            <div class="usuarios-form-section-heading">
                                <span class="material-symbols-outlined">
                                    shield_lock
                                </span>

                                <div>
                                    <h3>Seguridad</h3>
                                    <p>Establece las credenciales de acceso.</p>
                                </div>
                            </div>


                            <div class="row g-3">

                                {{-- CONTRASEÑA --}}
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="password"
                                               name="contrasenha"
                                               id="nuevo_contrasenha"
                                               class="form-control"
                                               placeholder="Contraseña"
                                               maxlength="250"
                                               autocomplete="new-password"
                                               required>

                                        <label for="nuevo_contrasenha">
                                            Contraseña
                                        </label>
                                    </div>
                                </div>


                                {{-- CONFIRMACIÓN --}}
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="password"
                                               name="contrasenha_confirmation"
                                               id="nuevo_contrasenha_confirmation"
                                               class="form-control"
                                               placeholder="Confirmar contraseña"
                                               maxlength="250"
                                               autocomplete="new-password"
                                               required>

                                        <label for="nuevo_contrasenha_confirmation">
                                            Confirmar contraseña
                                        </label>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="usuarios-modal-footer">

                        <button type="button"
                                class="btn btn-tonal"
                                data-bs-dismiss="modal">
                            Cancelar
                        </button>

                        <button type="submit" class="btn btn-primary">
                            <span class="material-symbols-outlined">check</span>
                            Crear usuario
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    {{-- =========================================================
         MODAL: MODIFICAR USUARIO
    ========================================================== --}}
    <div class="modal fade usuarios-modal"
         id="modalModificarUsuario"
         tabindex="-1"
         aria-labelledby="tituloModificarUsuario"
         aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">

            <div class="modal-content">

                <div class="usuarios-modal-header">

                    <div class="usuarios-modal-heading">
                        <div class="usuarios-modal-icon">
                            <span class="material-symbols-outlined">manage_accounts</span>
                        </div>

                        <div>
                            <h2 class="usuarios-modal-title" id="tituloModificarUsuario">
                                Modificar usuario
                            </h2>

                            <p class="usuarios-modal-subtitle">
                                Actualiza la información y los permisos de la cuenta.
                            </p>
                        </div>
                    </div>

                    <button type="button"
                            class="usuarios-modal-close"
                            data-bs-dismiss="modal"
                            aria-label="Cerrar">

                        <span class="material-symbols-outlined">close</span>
                    </button>

                </div>


                <form
                    id="formEditarUsuario"
                    method="POST"
                    data-url="{{ route('usuarios.actualizar', ['usuario' => '__ID__']) }}"
                    data-buscar-url="{{ route('usuarios.buscar', ['id' => '__ID__']) }}"
                    novalidate
                >

                    @csrf
                    @method('PUT')

                    <div
                        class="alert alert-danger d-none"
                        role="alert"
                        data-form-errors
                    ></div>

                    <div class="modal-body usuarios-modal-body">

                        {{-- CUENTA --}}
                        <div class="usuarios-form-section">

                            <div class="usuarios-form-section-heading">
                                <span class="material-symbols-outlined">
                                    admin_panel_settings
                                </span>

                                <div>
                                    <h3>Cuenta y permisos</h3>
                                    <p>Administra el rol y el estado del usuario.</p>
                                </div>
                            </div>


                            <div class="row g-3">

                                {{-- ROL --}}
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="id_rol"
                                                id="editar_rol"
                                                class="form-select"
                                                required>

                                            @foreach($roles as $rol)
                                                <option value="{{ $rol->id }}">
                                                    {{ $rol->rol }}
                                                </option>
                                            @endforeach

                                        </select>

                                        <label for="editar_rol">Rol del sistema</label>
                                    </div>
                                </div>


                                {{-- ESTADO --}}
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="id_estado"
                                                id="editar_estado"
                                                class="form-select"
                                                required>

                                            @foreach($estados as $estado)
                                                <option value="{{ $estado->id }}">
                                                    {{ $estado->estado }}
                                                </option>
                                            @endforeach

                                        </select>

                                        <label for="editar_estado">Estado de la cuenta</label>
                                    </div>
                                </div>

                            </div>

                        </div>


                        {{-- DATOS PERSONALES --}}
                        <div class="usuarios-form-section">

                            <div class="usuarios-form-section-heading">
                                <span class="material-symbols-outlined">
                                    badge
                                </span>

                                <div>
                                    <h3>Información personal</h3>

                                    <p>Datos de identificación y contacto.</p>
                                </div>
                            </div>

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text"
                                               name="nombres"
                                               id="editar_nombres"
                                               class="form-control"
                                               placeholder="Nombres"
                                               maxlength="100"
                                               required>

                                        <label for="editar_nombres">Nombres</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text"
                                               name="apellidos"
                                               id="editar_apellidos"
                                               class="form-control"
                                               placeholder="Apellidos"
                                               maxlength="100"
                                               required>

                                        <label for="editar_apellidos">Apellidos</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email"
                                               name="correo"
                                               id="editar_correo"
                                               class="form-control"
                                               placeholder="Correo electrónico"
                                               maxlength="150"
                                               required>

                                        <label for="editar_correo">
                                            Correo electrónico
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="editar_telefono" class="form-label">
                                        Teléfono
                                    </label>

                                    <div class="input-group usuarios-phone-field">
                                        <span class="input-group-text">+503</span>

                                        <input type="tel"
                                               name="telefono"
                                               id="editar_telefono"
                                               class="form-control"
                                               placeholder="0000 0000"
                                               maxlength="25"
                                               autocomplete="tel-national">
                                    </div>
                                </div>

                            </div>

                        </div>


                        {{-- SEGURIDAD --}}
                        <div class="usuarios-form-section">

                            <div class="usuarios-form-section-heading">
                                <span class="material-symbols-outlined">
                                    shield_lock
                                </span>

                                <div>
                                    <h3>Seguridad</h3>
                                    <p>Actualiza las credenciales cuando sea necesario.</p>
                                </div>
                            </div>

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="password"
                                               name="contrasenha"
                                               id="editar_contrasenha"
                                               class="form-control"
                                               placeholder="Nueva contraseña"
                                               maxlength="250"
                                               autocomplete="new-password">

                                        <label for="editar_contrasenha">
                                            Nueva contraseña
                                        </label>
                                    </div>

                                    <div class="usuarios-field-hint">
                                        Déjalo en blanco para conservar la contraseña actual.
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="password"
                                               name="contrasenha_confirmation"
                                               id="editar_confirmacion"
                                               class="form-control"
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

                    </div>


                    <div class="usuarios-modal-footer">

                        <button type="button"
                                class="btn btn-tonal"
                                data-bs-dismiss="modal">
                            Cancelar
                        </button>

                        <button type="submit" class="btn btn-primary">
                            <span class="material-symbols-outlined">save</span>
                            Guardar cambios
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    {{-- =========================================================
         MODAL: ELIMINAR USUARIO
    ========================================================== --}}
    <div class="modal fade usuarios-modal usuarios-delete-modal"
         id="modalEliminarUsuario"
         tabindex="-1"
         aria-labelledby="tituloEliminarUsuario"
         aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <div class="usuarios-delete-content">

                    <div class="usuarios-delete-icon">
                        <span class="material-symbols-outlined">person_remove</span>
                    </div>

                    <button type="button"
                            class="usuarios-modal-close usuarios-delete-close"
                            data-bs-dismiss="modal"
                            aria-label="Cerrar">

                        <span class="material-symbols-outlined">close</span>
                    </button>


                    <div class="usuarios-delete-heading">
                        <span class="usuarios-delete-overline">
                            Acción irreversible para el acceso
                        </span>

                        <h2 id="tituloEliminarUsuario">
                            ¿Eliminar este usuario?
                        </h2>

                        <p>
                            La cuenta dejará de estar disponible para acceder
                            a HealthWare. Podrás conservar su registro histórico.
                        </p>
                    </div>


                    <form
                        id="formEliminarUsuario"
                        method="POST"
                        data-url="{{ route('usuarios.eliminar', ['usuario' => '__ID__']) }}"
                        novalidate
                    >

                        @csrf
                        @method('DELETE')

                        <div
                            class="alert alert-danger d-none"
                            role="alert"
                            data-form-errors
                        ></div>

                        <div class="usuarios-delete-identity">

                            <div class="usuarios-delete-avatar">
                                <span class="material-symbols-outlined">
                                    account_circle
                                </span>
                            </div>

                            <div class="usuarios-delete-data">
                                <span class="usuarios-delete-name" id="eliminar_nombre">
                                    —
                                </span>

                                <span class="usuarios-delete-email" id="eliminar_correo">
                                    —
                                </span>

                                <span class="usuarios-delete-id">
                                    Identificador: #<span id="eliminar_id">—</span>
                                </span>
                            </div>

                        </div>


                        <div class="usuarios-delete-notice">
                            <span class="material-symbols-outlined">
                                info
                            </span>

                            <p>
                                Esta operación cambiará el estado de la cuenta
                                a eliminado. No equivale a borrar físicamente
                                sus registros de la base de datos.
                            </p>
                        </div>


                        <div class="usuarios-delete-actions">

                            <button type="button"
                                    class="btn btn-tonal"
                                    data-bs-dismiss="modal">
                                Conservar usuario
                            </button>

                            <button type="submit"
                                    class="btn usuarios-delete-confirm">
                                <span class="material-symbols-outlined">
                                    delete
                                </span>
                                Eliminar usuario
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

@endsection


@push('scripts')
    <script src="{{ asset('js/usuario/usuarios.js') }}"></script>
@endpush
