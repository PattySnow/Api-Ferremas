<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission = null)
    {
        if ($permission && !$request->user()->can($permission)) {
            return response()->json([
                'error' => 'No tienes los permisos para ejecutar esta acción.'
            ], 403);
        }

        return $next($request);
    }
}
