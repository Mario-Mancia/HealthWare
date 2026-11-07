@extends('layouts.app_layout')

@section('titulo', 'Diagnósticos')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/diagnosticos.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de diagnosticos</h1>
@endsection

@push('scripts')
    <script src="{{ asset('js/diagnostico/diagnosticos.js') }}"></script>
@endpush
