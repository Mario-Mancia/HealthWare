@extends('layouts.app_layout')

@section('titulo', 'Recetas')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/recetas.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de recetas médicas</h1>
@endsection

@push('scripts')
    <script src="{{ asset('js/recetas/recetas.js') }}"></script>
@endpush
