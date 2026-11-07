@extends('layouts.app_layout')

@section('titulo', 'Condiciones Médicas')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/condiciones_medicas.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de condiciones medicas</h1>
@endsection

@push('scripts')
    <script src="{{ asset('js/condicion_medica/condiciones_medicas.js') }}"></script>
@endpush
