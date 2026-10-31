<?php

use App\Http\Middleware\PreventBackHistory;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use \App\Http\Middleware\RolMiddleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(
            [
                'rol' => RolMiddleware::class,
                'no-cache' => PreventBackHistory::class,
            ]
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function(
           AuthenticationException $e,
           Request $request
        ) {
            if (!$request->expectsJson()) {
                return redirect()
                    ->guest(route('login'))
                    ->with('error', 'No puedes acceder a esta ruta sin iniciar sesión.');
            }

            return redirect()
                ->route('login')
                ->with('warning', 'Debe iniciar sesión para acceder al sistema.');
        });
    })->create();
