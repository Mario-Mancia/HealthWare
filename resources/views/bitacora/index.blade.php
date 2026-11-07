@extends('layouts.app_layout')

@section('titulo', 'Bitácora')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/bitacora.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de bitacora</h1>

    {{--
    VISTA: BITÁCORA DE ACTIVIDADES
    ------------------------------------------------------------
    OBJETIVO:
    Proporcionar al administrador un registro de las actividades
    realizadas dentro del sistema, permitiendo consultar eventos
    importantes relacionados con los usuarios y las operaciones.

    CONTENIDO GENERAL:
    1. Encabezado:
       - Título "Bitácora de actividades".
       - Descripción de la utilidad de la sección.
       - No incluir botones de creación, edición o eliminación.

    2. Panel de filtros:
       - Campo de búsqueda por usuario.
       - Selector de tipo de evento.
       - Campo de fecha inicial.
       - Campo de fecha final.
       - Botón para aplicar filtros.
       - Botón para limpiar los filtros.

    3. Tabla de actividades:
       - Identificador del registro.
       - Usuario responsable, cuando exista.
       - Tipo de evento.
       - Descripción de la actividad.
       - Dirección IP, si está almacenada.
       - Fecha y hora del evento.
       - Acción para consultar los detalles, si corresponde.

    4. Identificación visual de eventos:
       - Utilizar badges para distinguir los tipos de actividad.
       - Mantener colores consistentes para eventos de autenticación,
         creación, actualización y eliminación lógica.
       - No depender exclusivamente del color para identificar
         el tipo de evento; conservar también su etiqueta textual.

    5. Modal de detalles:
       - Mostrar el identificador del registro.
       - Usuario relacionado.
       - Tipo de evento.
       - Descripción completa.
       - Dirección IP, si existe.
       - Fecha y hora.
       - Información adicional almacenada, si corresponde.

    REGLAS DE NEGOCIO:
    - La bitácora es exclusivamente de consulta.
    - No permitir modificar ni eliminar registros desde esta vista.
    - Los registros deben conservar su integridad histórica.
    - El acceso está restringido al administrador.
    - Los registros pueden no tener un usuario asociado, por ejemplo,
      ante determinados eventos de autenticación.
    - Los filtros deben aplicarse desde el backend para evitar
      cargar innecesariamente todos los registros.

    INTEGRACIÓN:
    - Utilizar BitacoraController.
    - Consultar los registros mediante el método index.
    - Los filtros deben enviarse mediante GET.
    - Mantener la paginación y conservar los filtros aplicados.
    - No exponer información sensible almacenada en los registros.
--}}
@endsection

@push('scripts')

@endpush
