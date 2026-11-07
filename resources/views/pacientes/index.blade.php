@extends('layouts.app_layout')

@section('titulo', 'Pacientes')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/pacientes.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de Pacientes</h1>
@endsection

@push('scripts')
    <script src="{{ asset('js/paciente/pacientes.js') }}"></script>
@endpush
