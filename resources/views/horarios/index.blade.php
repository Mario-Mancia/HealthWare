@extends('layouts.app_layout')

@section('titulo', 'Horarios Doctores')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/horarios.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de horarios</h1>
@endsection

@push('scripts')
    <script src="{{ asset('js/horarios/horarios.js') }}"></script>
@endpush
