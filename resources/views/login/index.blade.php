@extends('layouts.auth_layout')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}"
@endpush

@section('contenido')
    <div class="row g-0 md3-pill-card mx-auto">

        {{-- Panel Izquierdo --}}
        <div class="col-lg-5 left-panel d-none d-lg-flex flex-column justify-content-center">
            <h2 class="fw-bold mb-3">Sistema Clínico</h2>
            <p class="mb-5 opacity-75">Gestión de expedientes médicos, citas y administración con estándares de seguridad.</p>

            <div class="d-flex align-items-start mb-4">
                <div class="step-icon-wrapper me-3">
                    <span class="material-symbols-outlined">badge</span>
                </div>
                <div>
                    <h5 class="fw-bold mb-1 fs-6">1. Identificación</h5>
                    <p class="mb-0 opacity-75 small">Accede con tu usuario institucional.</p>
                </div>
            </div>

            <div class="d-flex align-items-start mb-4">
                <div class="step-icon-wrapper me-3">
                    <span class="material-symbols-outlined">verified_user</span>
                </div>
                <div>
                    <h5 class="fw-bold mb-1 fs-6">2. Conexión Segura</h5>
                    <p class="mb-0 opacity-75 small">Sesión encriptada para protección de datos.</p>
                </div>
            </div>
        </div>

        {{-- Panel Derecho: Formulario --}}
        <div class="col-12 col-lg-7 right-panel d-flex flex-column justify-content-center">
            <div class="d-flex align-items-center mb-2">
                <span class="material-symbols-outlined me-2 fs-1 text-primary">account_circle</span>
                <h2 class="fw-bold mb-0">Iniciar Sesión</h2>
            </div>

            <p class="mb-4 form-subtitle">
                Por favor, introduce tus credenciales para ingresar al sistema.
            </p>

            <form action="{{ route('login') }}" method="POST" novalidate>
                @csrf

                {{-- Input Email --}}
                <div class="md3-input-group mb-4">
                    <input type="email" class="md3-input" id="email" name="correo" placeholder=" " required autofocus>
                    <label for="email" class="md3-label">Correo electrónico</label>
                </div>

                {{-- Input Password --}}
                <div class="md3-input-group mb-4">
                    <input type="password" class="md3-input" id="password" name="contrasenha" placeholder=" " required>
                    <label for="password" class="md3-label">Contraseña</label>
                    <span class="material-symbols-outlined md3-input-icon" id="togglePassword">visibility_off</span>
                </div>

                {{-- Opciones Extra --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="#" class="text-decoration-none fw-medium text-primary small">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                {{-- Botón Submit --}}
                <button type="submit" class="w-100 md3-btn d-flex align-items-center justify-content-center gap-2">
                    <span>Iniciar sesión</span>
                    <span class="material-symbols-outlined fs-5">arrow_forward</span>
                </button>
            </form>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/login-scripts.js') }}"></script>
@endpush
