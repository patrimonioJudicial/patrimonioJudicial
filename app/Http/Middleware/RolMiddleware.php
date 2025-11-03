<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RolMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = Auth::user();

        // Si no está autenticado
        if (!$user) {
            abort(403, 'Debes iniciar sesión.');
        }

        // Si el usuario no está activo
        if (isset($user->activo) && (int) $user->activo !== 1) {
            abort(403, 'Usuario inactivo.');
        }

        // Si el usuario es administrador → acceso total
        if ($user->rol?->nombre === 'administrador') {
            return $next($request);
        }

        // Si el rol del usuario está en los permitidos
        if (in_array($user->rol?->nombre, $roles)) {
            return $next($request);
        }

        // Caso contrario, acceso denegado
        abort(403, 'Acceso denegado. No tienes permisos para esta sección.');
    }
}
