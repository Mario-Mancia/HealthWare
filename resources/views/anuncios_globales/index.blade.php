@extends('layouts.app_layout')

@section('titulo', 'Anuncios')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/anuncios_globales.css')}}"
@endpush

@section('contenido')
    <h1>Ruta de anuncios globales</h1>
@endsection

@push('scripts')
    <script src="{{ asset('js/anuncios_globales/anuncios_globales.js') }}"></script>
@endpush
