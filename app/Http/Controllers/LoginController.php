<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
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

            if (strtolower($usuario->estado->estado) !== 'activo') {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()
                    ->route('login')
                    ->with(
                        'warning',
                        'Su cuenta se encuentra inactiva, contacte al administrador.'
                    );
            }

            $request->session()->regenerate();

            return redirect()
                ->intended('/')
                ->with(
                    'success',
                    'Bienvenido de nuevo, ' . $usuario->nombres
                );
        }

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
