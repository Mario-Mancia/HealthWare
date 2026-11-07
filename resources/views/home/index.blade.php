{{-- Vista generada por reestructurar-vistas.sh: home.index --}}
@extends('layouts.app_layout')

@section('titulo', 'Home')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('contenido')
    <h1 class="fw-bold fs-1">Ruta: home.index</h1>
@endsection

@push('scripts')
    <script src="{{ asset('js/home/recetas.js') }}"></script>
@endpush
