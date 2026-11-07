@extends('layouts.app_layout')

@section('titulo', 'Estados')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/estados.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de estados</h1>
@endsection

@push('scripts')
    <script src="{{ asset('js/estado/estados.js') }}"></script>
@endpush
