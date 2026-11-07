@extends('layouts.app_layout')

@section('titulo', 'Roles')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/roles.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de roles</h1>
@endsection

@push('scripts')
    <script src="{{ asset('js/rol/roles.js') }}"></script>
@endpush
