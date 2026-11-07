@extends('layouts.app_layout')

@section('titulo', 'Tipos Sanguineos')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/tipos_sanguineos.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de tipos sanguineos</h1>
@endsection

@push('scripts')
    <script src="{{ asset('js/tipo_sanguineo/tipos_sanguineos.js') }}"></script>
@endpush
