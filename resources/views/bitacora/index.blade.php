@extends('layouts.app_layout')

@section('titulo', 'Bitácora')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/bitacora.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de bitacora</h1>
@endsection

@push('scripts')

@endpush
