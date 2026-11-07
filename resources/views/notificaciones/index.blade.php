@extends('layouts.app_layout')

@section('titulo', 'Notificaciones')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/notificaciones.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de notificaciones</h1>

    {{--
    VISTA: CENTRO DE NOTIFICACIONES
    ------------------------------------------------------------
    OBJETIVO:
    Permitir que cada usuario consulte las notificaciones que ha
    recibido dentro del sistema y gestione su estado de lectura.

    CONTENIDO GENERAL:
    1. Encabezado:
       - Título "Notificaciones".
       - Descripción de la sección.
       - Indicador de notificaciones pendientes de lectura.

    2. Resumen:
       - Cantidad total de notificaciones.
       - Cantidad de notificaciones leídas.
       - Cantidad de notificaciones pendientes.
       - Los indicadores deben calcularse en el backend.

    3. Filtros:
       - Todas las notificaciones.
       - Notificaciones leídas.
       - Notificaciones no leídas.
       - Búsqueda por título o contenido, si esos campos existen.
       - Filtro por fecha de recepción.

    4. Listado de notificaciones:
       - Título.
       - Resumen del contenido.
       - Fecha de recepción.
       - Indicador de lectura.
       - Acción para consultar el contenido completo.
       - Acción para marcar como leída, si está implementada.

    5. Modal de visualización:
       - Título completo.
       - Contenido completo.
       - Fecha de recepción.
       - Estado de lectura.
       - Acción para marcar como leída, si corresponde.

    6. Estados visuales:
       - Diferenciar notificaciones leídas y no leídas.
       - Destacar las notificaciones pendientes sin utilizar
         elementos visuales excesivamente llamativos.
       - Mostrar un mensaje cuando no existan notificaciones.

    REGLAS DE NEGOCIO:
    - Cada usuario solamente puede consultar sus propias
      notificaciones.
    - No permitir que un usuario consulte o modifique notificaciones
      pertenecientes a otra cuenta.
    - Marcar como leída una notificación no debe eliminarla.
    - El estado de lectura debe persistirse en la base de datos
      cuando esta funcionalidad esté implementada.
    - Las notificaciones deben conservar su historial.
    - Si se permite generar notificaciones desde el panel, restringir
      esa operación a los roles autorizados.

    INTEGRACIÓN:
    - Utilizar NotificacionController.
    - Aprovechar las rutas existentes:
        - index
        - todos
        - insertar
        - buscar
        - actualizar
    - Las operaciones de lectura y actualización deben validar
      la propiedad de la notificación.
    - El indicador de notificaciones pendientes puede integrarse
      posteriormente con el encabezado global del sistema.
--}}
@endsection

@push('scripts')
    <script src="{{ asset('js/notificacion/notificaciones.js') }}"></script>
@endpush
