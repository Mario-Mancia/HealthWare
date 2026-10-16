@extends('layouts.app_layout')

@section('titulo', 'Bitácora')

@push('estilos')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('css/usuarios.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bitacora.css') }}">
@endpush

@section('contenido')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h1 class="h4 mb-0 fw-bold page-title d-flex align-items-center gap-2">

            <span class="material-symbols-outlined fs-2 page-title-icon">
                history
            </span>

            Bitácora del sistema

        </h1>

    </div>


    <div class="card md3-filter-card shadow-sm mb-4">

        <div class="card-body">

            <form action="{{ route('bitacora') }}" method="GET">

                <div class="row g-3 align-items-end">

                    {{-- EVENTO --}}
                    <div class="col-12 col-md-4">

                        <label for="evento" class="md3-filter-label">
                            Evento
                        </label>

                        <select
                            name="evento"
                            id="evento"
                            class="form-select md3-filter-select"
                        >

                            <option value="">
                                Todos los eventos
                            </option>

                            @foreach($eventos as $evento)
                                <option
                                    value="{{ $evento }}"
                                    {{ request('evento') === $evento ? 'selected' : '' }}
                                >
                                    {{ $evento }}
                                </option>
                            @endforeach

                        </select>

                    </div>


                    {{-- FECHA DESDE --}}
                    <div class="col-12 col-md-3">

                        <label for="fecha_desde" class="md3-filter-label">
                            Fecha desde
                        </label>

                        <input
                            type="date"
                            name="fecha_desde"
                            id="fecha_desde"
                            class="form-control md3-filter-control"
                            value="{{ request('fecha_desde') }}"
                        >

                    </div>


                    {{-- FECHA HASTA --}}
                    <div class="col-12 col-md-3">

                        <label for="fecha_hasta" class="md3-filter-label">
                            Fecha hasta
                        </label>

                        <input
                            type="date"
                            name="fecha_hasta"
                            id="fecha_hasta"
                            class="form-control md3-filter-control"
                            value="{{ request('fecha_hasta') }}"
                        >

                    </div>


                    {{-- BOTONES --}}
                    <div class="col-12 col-md-2">

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-filter flex-grow-1"
                                title="Aplicar filtros"
                            >
                                <span class="material-symbols-outlined align-middle fs-5">
                                    filter_alt
                                </span>
                            </button>

                            <a
                                href="{{ route('bitacora') }}"
                                class="btn btn-filter-clear"
                                title="Limpiar filtros"
                            >
                                <span class="material-symbols-outlined align-middle fs-5">
                                    filter_alt_off
                                </span>
                            </a>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- ============================================================
         TABLA DE BITÁCORA
         ============================================================ --}}
    <div class="card md3-card-table shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive p-3 p-md-4">

                <table
                    id="tbl"
                    class="table md3-table align-middle mb-0"
                >

                    <thead class="md3-table-header">

                    <tr>

                        <th class="border-0">
                            #
                        </th>

                        <th class="border-0">
                            Usuario
                        </th>

                        <th class="border-0">
                            Evento
                        </th>

                        <th class="border-0">
                            Módulo
                        </th>

                        <th class="border-0">
                            Registro
                        </th>

                        <th class="border-0">
                            Descripción
                        </th>

                        <th class="border-0">
                            IP
                        </th>

                        <th class="border-0">
                            Fecha y hora
                        </th>

                    </tr>

                    </thead>


                    <tbody>

                    @foreach($bitacoras as $bitacora)

                        <tr>

                            {{-- ID --}}
                            <td class="opacity-75 fw-medium">
                                {{ $bitacora->id }}
                            </td>


                            {{-- USUARIO --}}
                            <td class="fw-medium">
                                {{ $bitacora->usuario_nombre ?? 'Sistema / No identificado' }}
                            </td>


                            {{-- EVENTO --}}
                            <td>

                                @php
                                    $claseEvento = match ($bitacora->evento) {
                                        'LOGIN_OK' => 'md3-badge-login-ok',
                                        'LOGIN_FALLIDO' => 'md3-badge-login-fallido',
                                        'LOGOUT' => 'md3-badge-logout',
                                        'CREADO' => 'md3-badge-creado',
                                        'ACTUALIZADO' => 'md3-badge-actualizado',
                                        'ELIMINADO' => 'md3-badge-eliminado',
                                        default => 'md3-badge-default',
                                    };
                                @endphp

                                <span class="md3-badge {{ $claseEvento }}">
                                    {{ $bitacora->evento }}
                                </span>

                            </td>


                            {{-- MÓDULO --}}
                            <td>

                                <span class="md3-badge md3-badge-secondary">
                                    {{ $bitacora->modulo ?? 'N/A' }}
                                </span>

                            </td>


                            {{-- REGISTRO --}}
                            <td class="fw-medium">
                                {{ $bitacora->registro_id ?? 'N/A' }}
                            </td>


                            {{-- DESCRIPCIÓN --}}
                            <td class="descripcion">
                                {{ $bitacora->descripcion ?? 'Sin descripción' }}
                            </td>


                            {{-- IP --}}
                            <td>
                                {{ $bitacora->ip_address ?? 'N/A' }}
                            </td>


                            {{-- FECHA --}}
                            <td>
                                {{ $bitacora->created_at?->format('d/m/Y H:i:s') ?? 'N/A' }}
                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection
