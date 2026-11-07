@extends('layouts.app_layout')

@section('titulo', 'Detalle Cita')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/citas.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de detalle citas</h1>
@endsection

@push('scripts')
    <script src="{{ asset('js/cita/citas.js') }}"></script>
@endpush
