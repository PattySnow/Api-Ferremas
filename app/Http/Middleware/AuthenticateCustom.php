<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class AuthenticateCustom extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

     
    protected function unauthenticated($request, array $guards)
    {
        if($request->is('api/*')) {
            $ErrorResponse = [
                'success' => false,
                'message' =>'Invalid Token',
            ];
            abort(response()->json($ErrorResponse, 403));
        }
    
        if (! $request->expectsJson()) {
            return route('login');
        } 
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo(Request $request)
    {
        //
    }
}
