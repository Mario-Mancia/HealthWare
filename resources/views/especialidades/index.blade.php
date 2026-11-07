@extends('layouts.app_layout')

@section('titulo', 'Especialidades')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/especialidades.css.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de especialidades</h1>


    {{--
        VISTA: Catálogo de especialidades médicas.

        OBJETIVO:
        Administrar las especialidades profesionales que pueden
        asociarse a los doctores.

        CONTENIDO:
        - Encabezado.
        - Botón para agregar especialidad.
        - Tabla de especialidades.
        - Columnas: identificador, nombre y descripción.
        - Búsqueda por nombre.
        - Modal de creación.
        - Modal de edición.
        - Confirmación de eliminación.

        INTERACCIÓN:
        - Crear, consultar, actualizar y eliminar especialidades.
        - Mostrar validaciones y notificaciones.

        CONSIDERACIONES:
        - Los doctores se relacionan con este catálogo.
        - Evitar eliminar especialidades que estén siendo utilizadas.
        - Los tres roles pueden consultar el catálogo.
        - La administración corresponde al administrador.
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/especialidad/especialidades.js') }}"></script>
@endpush
