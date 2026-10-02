{{-- Componente reutilizable de toast para mostrar notificaciones al usuario --}}


    <div class="toast-container position-fixed bottom-0 end-0 p-4" style="z-index: 1056;">

        @foreach (['success', 'error', 'warning', 'info'] as $msgType)
            @if(session($msgType))
                @php
                    $bsClass = '';
                    $iconName = '';

                    switch($msgType) {
                        case 'success':
                            $bsClass = 'success';
                            $iconName = 'check_circle';
                            break;
                        case 'error':
                            $bsClass = 'danger';
                            $iconName = 'error';
                            break;
                        case 'warning':
                            $bsClass = 'warning';
                            $iconName = 'warning';
                            break;
                        case 'info':
                            $bsClass = 'info';
                            $iconName = 'info';
                            break;
                    }
                @endphp


                <div class="toast align-items-center bg-body border border-{{ $bsClass }} rounded-3 shadow-sm mb-3" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex px-3 py-3">
                        <!-- Icono Material -->
                        <span class="material-symbols-outlined text-{{ $bsClass }} me-3 fs-5">
                            {{ $iconName }}
                        </span>

                        <!-- Mensaje -->
                        <div class="toast-body p-0 fw-medium text-{{ $bsClass }} flex-grow-1 align-self-center">
                            {{ session($msgType) }}
                        </div>

                        <!-- Botón cerrar (Opcional, Bootstrap lo alinea a la derecha) -->
                        <button type="button" class="btn-close ms-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
                    </div>
                </div>
            @endif
        @endforeach

        <!-- Toasts para errores de validación de Laravel -->
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="toast align-items-center bg-body border border-danger rounded-3 shadow-sm mb-3" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex px-3 py-3">
                        <span class="material-symbols-outlined text-danger me-3 fs-5">
                            error
                        </span>
                        <div class="toast-body p-0 fw-medium text-danger flex-grow-1 align-self-center">
                            {{ $error }}
                        </div>
                        <button type="button" class="btn-close ms-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

