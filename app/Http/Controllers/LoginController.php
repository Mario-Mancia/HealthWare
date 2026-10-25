<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Bitacora;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function index(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('login.index');
    }

    /**
     * Procesa el inicio de sesión.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        // Normalizar correo electrónico y generar clave del limitador.
        $correo = strtolower(trim($request->correo));

        $claveLimite = 'login:' . hash(
                'sha256',
                $correo . '|' . $request->ip()
            );

        // Verificar si se alcanzó el límite de intentos fallidos.
        if (RateLimiter::tooManyAttempts($claveLimite, 5)) {

            Bitacora::registrar(
                Bitacora::LOGIN_FALLIDO,
                'LOGIN',
                null,
                'Intento de inicio de sesión bloqueado por límite de intentos.',
                $correo
            );

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Demasiados intentos. Intente nuevamente más tarde.'
                );
        }


        $credenciales = [
            'correo' => $correo,
            'password' => $request->contrasenha,
        ];


        if (Auth::attempt($credenciales)) {

            $usuario = Auth::user();


            $estado = strtolower($usuario->estado?->estado ?? '');


            if (!in_array($estado, ['activo', 'inactivo', 'eliminado'], true)
                || in_array($estado, ['inactivo', 'eliminado'], true)) {

                // Registrar el rechazo antes de cerrar la sesión.
                Bitacora::registrar(
                    Bitacora::LOGIN_FALLIDO,
                    'LOGIN',
                    $usuario->id,
                    'Inicio de sesión rechazado: cuenta ' .
                    ($estado ?: 'sin estado válido') . '.'
                );

                // Contabilizar el intento fallido.
                RateLimiter::hit($claveLimite, 300);

                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()
                    ->route('login')
                    ->with(
                        'error',
                        'No fue posible iniciar sesión. Verifique sus credenciales o contacte al administrador.'
                    );
            }

            // Regenerar la sesión para prevenir session fixation.
            $request->session()->regenerate();


            RateLimiter::clear($claveLimite);


            Bitacora::registrar(
                Bitacora::LOGIN_OK,
                'LOGIN',
                $usuario->id,
                'Inicio de sesión exitoso.'
            );

            return redirect()
                ->intended('/')
                ->with(
                    'success',
                    'Bienvenido de nuevo, ' . $usuario->nombres
                );
        }


        Bitacora::registrar(
            Bitacora::LOGIN_FALLIDO,
            'LOGIN',
            null,
            'Intento de inicio de sesión fallido: credenciales incorrectas.',
            $correo
        );


        RateLimiter::hit($claveLimite, 300);

        return redirect()
            ->route('login')
            ->with(
                'error',
                'Credenciales incorrectas. Verifique su correo o contraseña.'
            );
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function logout(Request $request): RedirectResponse
    {
        Bitacora::registrar(
            Bitacora::LOGOUT,
            'LOGIN',
            Auth::user()?->id_usuario,
            'Cierre de sesión.'
        );

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Sesión cerrada correctamente.'
            );
    }
}
