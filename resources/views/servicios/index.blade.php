@extends('layouts.app_layout')

@section('titulo', 'Servicios')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/servicios.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de servicios</h1>

    {{--
    VISTA: CATÁLOGO DE SERVICIOS
    ------------------------------------------------------------
    OBJETIVO:
    Permitir consultar y administrar los servicios médicos o
    profesionales que ofrece la clínica y que posteriormente
    pueden utilizarse en el proceso de facturación.

    CONTENIDO GENERAL:
    1. Encabezado:
       - Título "Servicios".
       - Descripción de la sección.
       - Botón para registrar un servicio, según los permisos
         del usuario autenticado.

    2. Tabla de servicios:
       - Identificador.
       - Nombre del servicio.
       - Descripción.
       - Precio.
       - Acciones disponibles.

    3. Filtros:
       - Búsqueda por nombre.
       - Búsqueda por descripción.
       - Filtro por precio, si resulta necesario.

    4. Modal de registro:
       - Campo para el nombre del servicio.
       - Campo para la descripción.
       - Campo para el precio.
       - Botón para guardar.

    5. Modal de edición:
       - Cargar la información del servicio seleccionado.
       - Permitir actualizar nombre, descripción y precio.
       - Validar que el precio sea un valor numérico válido
         y no negativo.

    6. Modal de visualización:
       - Mostrar los datos completos del servicio.
       - Mostrar su precio actual.

    REGLAS DE NEGOCIO:
    - No permitir nombres vacíos.
    - Validar la longitud máxima de los campos según la migración.
    - El precio debe ser numérico y no negativo.
    - Los servicios se utilizan como referencia al crear
      los detalles de una factura.
    - Si un servicio ya fue utilizado en una factura, no se debe
      alterar el precio histórico de dicha factura al modificar
      el precio actual del catálogo.
    - Evitar eliminar físicamente servicios que estén relacionados
      con registros históricos.
    - Restringir las operaciones administrativas según el rol.

    INTEGRACIÓN:
    - Utilizar ServicioController.
    - Consultar y administrar los servicios mediante las rutas
      existentes del módulo servicios.
    - La información debe estar disponible para el módulo
      de facturación.
    - Los cálculos y validaciones importantes deben realizarse
      en el backend.
--}}
@endsection

@push('scripts')
    <script src="{{ asset('js/servicio/servicios.js') }}"></script>
@endpush
