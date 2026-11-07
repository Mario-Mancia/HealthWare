@extends('layouts.app_layout')

@section('titulo', 'Notificaciones')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/notificaciones.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de notificaciones</h1>
@endsection

@push('scripts')
    <script src="{{ asset('js/notificacion/notificaciones.js') }}"></script>
@endpush
