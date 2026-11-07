@extends('layouts.app_layout')

@section('titulo', 'Perfil Doctor')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/doctores.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de perfil de doctores</h1>
@endsection

@push('scripts')
    <script src="{{ asset('js/doctor/doctores.js') }}"></script>
@endpush
