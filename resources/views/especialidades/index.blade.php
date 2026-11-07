@extends('layouts.app_layout')

@section('titulo', 'Especialidades')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/especialidades.css.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de especialidades</h1>
@endsection

@push('scripts')
    <script src="{{ asset('js/especialidad/especialidades.js') }}"></script>
@endpush
