@extends('layouts.app_layout')

@section('titulo', 'Perfil Paciente')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/pacientes.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de perfil de paciente</h1>


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
