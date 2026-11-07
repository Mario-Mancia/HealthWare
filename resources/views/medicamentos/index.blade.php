@extends('layouts.app_layout')

@section('titulo', 'Medicamentos')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/medicamentos.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de medicamentos</h1>


    {{--
        VISTA: Catálogo de medicamentos.

        OBJETIVO:
        Administrar los medicamentos disponibles para su selección
        al emitir recetas médicas.

        CONTENIDO:
        - Encabezado.
        - Botón para registrar medicamento.
        - Buscador por nombre o principio activo.
        - Tabla con:
          * Identificador.
          * Nombre.
          * Principio activo.
          * Presentación.
          * Concentración.
          * Descripción.
        - Modal de creación.
        - Modal de edición.
        - Modal de consulta detallada.
        - Confirmación de eliminación.

        FORMULARIO:
        - Nombre.
        - Principio activo.
        - Presentación.
        - Concentración.
        - Descripción.

        INTERACCIÓN:
        - Crear, consultar, actualizar y eliminar medicamentos.
        - Permitir su selección al elaborar recetas.

        CONSIDERACIONES:
        - Los medicamentos se relacionan con recetas mediante recetas_items.
        - No confundir este catálogo con un módulo de inventario.
        - No implementar control de existencias si no está contemplado
          en el modelo de datos.
        - Consulta para administrador y médico.
        - Administración del catálogo exclusiva del administrador.
    --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/medicamento/medicamentos.css.js') }}"></script>
@endpush
