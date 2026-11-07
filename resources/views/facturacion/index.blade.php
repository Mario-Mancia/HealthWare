@extends('layouts.app_layout')

@section('titulo', 'Factura')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/facturacion.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de facturas</h1>
@endsection

@push('scripts')
    <script src="{{ asset('js/facturacion/facturas.js') }}"></script>
@endpush
