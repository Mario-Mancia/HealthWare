@extends('layouts.app_layout')

@section('titulo', 'Registros Médicos')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/registros_medicos.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de Registros médicos</h1>
@endsection

@push('scripts')
    <script src="{{ asset('js/registro_medico/registroMedico.js') }}"></script>
@endpush
