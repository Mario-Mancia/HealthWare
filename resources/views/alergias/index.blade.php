@extends('layouts.app_layout')

@section('titulo', 'Alergias')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/alergias.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de alergias</h1>
@endsection

@push('scripts')
    <script src="{{ asset('js/alergia/alergias.js') }}"></script>
@endpush
