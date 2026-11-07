@extends('layouts.app_layout')

@section('titulo', 'Pacientes')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/pacientes.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de Pacientes</h1>


    {{--
        VISTA: Gestión de pacientes.

        OBJETIVO:
        Mostrar el listado general de pacientes registrados en la clínica
        y permitir las operaciones administrativas correspondientes.

        CONTENIDO:
        1. ENCABEZADO:
           - Título "Pacientes".
           - Descripción breve.
           - Botón para registrar un nuevo paciente.

        2. FILTROS:
           - Búsqueda por nombres, apellidos o DUI.
           - Filtro por sexo.
           - Filtro por estado del paciente, si aplica.
           - Botón para limpiar filtros.

        3. TABLA:
           - Identificador del paciente.
           - Nombres y apellidos.
           - DUI.
           - Fecha de nacimiento o edad.
           - Teléfono.
           - Correo electrónico, si está disponible.
           - Acciones.

        4. MODALES:
           - Registrar paciente.
           - Editar información del paciente.
           - Confirmar eliminación lógica.
           - Consultar información resumida, si resulta conveniente.

        INTERACCIÓN:
        - Acceso al perfil del paciente.
        - Acceso a su expediente clínico.
        - Envío de formularios mediante las rutas correspondientes.
        - Validación y presentación de errores.
        - Actualización de la tabla después de operaciones exitosas.

        CONSIDERACIONES:
        - No eliminar físicamente pacientes si existen relaciones clínicas.
        - La eliminación debe respetar la lógica de estados definida.
        - Los datos clínicos no deben exponerse en esta tabla administrativa.
        - El acceso a operaciones depende del rol autenticado.
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/paciente/pacientes.js') }}"></script>
@endpush
