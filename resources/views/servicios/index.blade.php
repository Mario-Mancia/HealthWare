@extends('layouts.app_layout')

@section('titulo', 'Servicios')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/servicios.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de servicios</h1>
@endsection

@push('scripts')
    <script src="{{ asset('js/servicio/servicios.js') }}"></script>
@endpush
