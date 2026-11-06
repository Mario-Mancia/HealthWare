<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolMiddleware
{
    /**
     * Verifica que el usuario esté activo y tenga
     * uno de los roles requeridos para acceder a la ruta.
     */
    public function handle(
        Request $request,
        Closure $next,
                ...$roles
    ): Response {
        $usuario = auth()->user();

        /**
         * El middleware "auth" debe ejecutarse antes que este middleware.
         * Esta comprobación funciona como medida adicional de seguridad.
         */
        if (!$usuario) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Debe iniciar sesión para acceder al sistema.'
                );
        }

        /**
         * Verificamos que el usuario tenga un estado válido y activo.
         */
        if (
            !$usuario->estado ||
            strtolower(trim($usuario->estado->estado)) !== 'activo'
        ) {
            auth()->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with(
                    'warning',
                    'Su cuenta se encuentra inactiva, contacte al administrador.'
                );
        }

        /**
         * Si no se especifican roles, cualquier usuario
         * autenticado y activo puede continuar.
         */
        if (empty($roles)) {
            return $next($request);
        }

        /**
         * Obtenemos el rol del usuario y normalizamos su nombre.
         */
        $rolUsuario = $usuario->rol
            ? strtolower(trim($usuario->rol->rol))
            : null;

        /**
         * Normalizamos los roles permitidos y comprobamos
         * si el usuario tiene alguno de ellos.
         */
        $rolesPermitidos = array_map(
            fn ($rol) => strtolower(trim($rol)),
            $roles
        );

        if (
            $rolUsuario !== null &&
            in_array($rolUsuario, $rolesPermitidos, true)
        ) {
            return $next($request);
        }

        /**
         * El usuario está autenticado y activo, pero no tiene
         * permisos suficientes para acceder a esta sección.
         */
        return redirect()
            ->route('home')
            ->with(
                'error',
                'No tiene permisos para acceder a esta sección.'
            );
    }
}
