<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized - No Autenticado'], 401);
        }

        $user = Auth::user();

        if ($user->hasAnyRole($roles)) {
            return $next($request);
        }

        return response()->json(['message' => 'No tienes los permisos necesarios para acceder a este sitio'], 403);
    }
}
