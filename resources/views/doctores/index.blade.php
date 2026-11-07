@extends('layouts.app_layout')

@section('titulo', 'Doctores')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/doctores.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de doctores</h1>


    {{--
        VISTA: Gestión y consulta de doctores.

        OBJETIVO:
        Mostrar el personal médico registrado y permitir administrar
        su información profesional.

        CONTENIDO:
        1. ENCABEZADO:
           - Título "Doctores".
           - Botón para registrar un doctor, únicamente para administradores.

        2. FILTROS:
           - Nombre del doctor.
           - Especialidad.
           - Estado de su cuenta o registro.

        3. TABLA:
           - Identificador del doctor.
           - Nombre completo.
           - DUI.
           - Número de licencia profesional.
           - Especialidad.
           - Teléfono o correo asociado.
           - Acciones.

        4. MODALES:
           - Registrar doctor.
           - Editar información profesional.
           - Consultar información resumida.

        5. REGISTRO DEL DOCTOR:
           - Seleccionar o crear el usuario relacionado.
           - Asociar especialidad.
           - DUI.
           - Número de licencia.
           - Fecha de nacimiento.
           - Sexo.
           - Descripción profesional.

        6. ACCIONES:
           - Consultar perfil.
           - Editar datos.
           - Consultar horarios.
           - Consultar agenda.

        CONSIDERACIONES:
        - El doctor está relacionado con un usuario del sistema.
        - No confundir el registro profesional con la cuenta de acceso.
        - La creación del doctor debe contemplar la relación con su usuario.
        - No eliminar físicamente doctores.
        - Las operaciones de creación y edición corresponden al administrador.
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/doctor/doctores.js') }}"></script>
@endpush
