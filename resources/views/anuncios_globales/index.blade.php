@extends('layouts.app_layout')

@section('titulo', 'Anuncios')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/anuncios_globales.css')}}"
@endpush

@section('contenido')
    <h1>Ruta de anuncios globales</h1>

    {{--
    VISTA: GESTIÓN DE ANUNCIOS GLOBALES
    ------------------------------------------------------------
    OBJETIVO:
    Permitir al administrador publicar y gestionar anuncios que
    estarán disponibles para todos los usuarios del sistema.

    CONTENIDO GENERAL:
    1. Encabezado:
       - Título "Anuncios globales".
       - Descripción breve de la sección.
       - Botón para registrar un nuevo anuncio (solo administrador).

    2. Tabla de anuncios:
       - Identificador.
       - Título del anuncio.
       - Contenido (mostrar una versión resumida).
       - Fecha de publicación.
       - Estado del anuncio, si está contemplado en el modelo.
       - Acciones disponibles.

    3. Filtros:
       - Búsqueda por título o contenido.
       - Filtrado por estado, si corresponde.
       - Filtrado por fecha de publicación.

    4. Modal de registro:
       - Campo para el título.
       - Área de texto para el contenido.
       - Estado o visibilidad, si el modelo lo contempla.
       - Botón para publicar o guardar.

    5. Modal de edición:
       - Permitir modificar el título y contenido.
       - Permitir actualizar el estado, si corresponde.
       - Cargar los datos del anuncio seleccionado.

    6. Modal de visualización:
       - Mostrar el contenido completo del anuncio.
       - Mostrar la fecha de publicación y su estado.

    REGLAS DE NEGOCIO:
    - Todos los roles autorizados pueden consultar los anuncios.
    - Solamente el administrador puede registrar o modificar anuncios.
    - No permitir contenido vacío.
    - La visibilidad de los anuncios debe respetar el estado definido
      en el modelo.
    - No implementar eliminación física si no está contemplada.

    INTEGRACIÓN:
    - Utilizar las rutas del módulo anuncios-globales.
    - Los datos deben proceder del controlador correspondiente.
    - Mantener la estructura visual del sistema y utilizar los
      componentes globales de notificaciones.
--}}
@endsection

@push('scripts')
    <script src="{{ asset('js/anuncios_globales/anuncios_globales.js') }}"></script>
@endpush
