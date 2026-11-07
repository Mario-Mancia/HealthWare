@extends('layouts.app_layout')

@section('titulo', 'Medicamentos')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/medicamentos.css') }}"
@endpush

@section('contenido')
    <h1>Ruta de medicamentos</h1>
@endsection

@push('scripts')
    <script src="{{ asset('js/medicamento/medicamentos.css.js') }}"></script>
@endpush
