@extends('layouts.app_layout')

@section('titulo', 'Perfil Paciente')

@section('contenido')

    <div class="hw-page">

        <header class="hw-header">
            <div class="hw-heading">
                <div class="hw-heading-icon">
                    <span class="material-symbols-outlined">person</span>
                </div>
                <div>
                    <h1 class="hw-title">Perfil del paciente</h1>
                    <p class="hw-subtitle">Información personal y administrativa del paciente.</p>
                </div>
            </div>
        </header>

        <section class="hw-surface">
            <div class="hw-placeholder">
                <span class="material-symbols-outlined">construction</span>
                <h2>Pantalla pendiente de conectar</h2>
                <p>
                    El diseño base ya usa el layout y los componentes de HealthWare.
                    Falta definir la ruta y la acción del controlador que cargan sus datos.
                </p>
            </div>
        </section>

    </div>

    {{--
        VISTA: Perfil individual del paciente.

        OBJETIVO:
        Presentar la información personal y administrativa de un paciente
        seleccionado, sin convertir esta pantalla en su expediente clínico.

        CONTENIDO:
        1. ENCABEZADO:
           - Nombres y apellidos.
           - Fotografía o avatar, si se implementa.
           - Identificador del paciente.
           - Estado de su registro.

        2. INFORMACIÓN PERSONAL:
           - Nombres y apellidos.
           - DUI.
           - Fecha de nacimiento.
           - Sexo.
           - Teléfono.
           - Correo electrónico.
           - Dirección, si está contemplada en el modelo.

        3. INFORMACIÓN COMPLEMENTARIA:
           - Tipo sanguíneo.
           - Fecha de registro.
           - Información administrativa adicional.

        4. ACCIONES:
           - Editar información personal.
           - Consultar expediente clínico.
           - Consultar historial de citas.
           - Regresar al listado de pacientes.

        CONSIDERACIONES:
        - Mantener separada la información personal de la información clínica.
        - No duplicar formularios completos del expediente.
        - Mostrar únicamente información permitida para el rol.
        - Recibir el paciente seleccionado desde el controlador.

        NOTA:
        - Definir posteriormente la ruta y acción del controlador que
          devolverán esta vista con los datos del paciente.
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/paciente/pacientes.js') }}"></script>
@endpush
