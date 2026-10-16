<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Bitacora;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
        $credenciales = [
            'correo' => $request->correo,
            'password' => $request->contrasenha,
        ];

        if (Auth::attempt($credenciales)) {
            $usuario = Auth::user();

            if (strtolower($usuario->estado->estado) == 'inactivo') {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()
                    ->route('login')
                    ->with('warning', 'Su cuenta se encuentra inactiva, contacte al administrador.');
            };

            if (strtolower($usuario->estado->estado) == 'eliminado') {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()
                    ->route('login')
                    ->with('error', 'Su cuenta ya no está disponible en el sistema.');
            }

            $request->session()->regenerate();

            Bitacora::registrar(
                Bitacora::LOGIN_OK,
                'LOGIN',
                $usuario->id_usuario,
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
            'Intento de inicio de sesión fallido.',
            $request->correo
        );

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
