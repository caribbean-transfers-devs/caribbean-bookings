<?php

namespace App\Http\Middleware;

use Closure;
use Debugbar;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DebugbarMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Rutas donde se ocultará Debugbar
        $excludedRoutes = ['login', 'register', 'password.request'];

        // Verificar si la ruta actual está en la lista
        if (in_array($request->route()->getName(), $excludedRoutes)) {
            Debugbar::disable();
        }

        // Solo mostrar Debugbar a usuarios con un rol específico
        if (auth()->check()) {
            $user = auth()->user();
            // dump(session()->get('roles')['roles']);
            $roles = session()->get('roles')['roles'];

            // Cambia "admin" por el rol o condición que necesites
            if ( !in_array(1, $roles) ) { 
                Debugbar::disable();
            }
        } else {
            Debugbar::disable(); // Deshabilitar si no hay usuario autenticado
        }

        return $next($request);
    }
}
