@extends('layouts.app_layout')

@section('titulo', 'Perfil Paciente')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/pacientes.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de perfil de paciente</h1>
@endsection

@push('scripts')
    <script src="{{ asset('js/paciente/pacientes.js') }}"></script>
@endpush
