<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ShippingOrder;

class CheckShippingOrderAccess
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::guard('api')->check()) {
            return response()->json(['message' => 'Unauthorized - No Autenticado'], 403);
        }

        // Obtener el usuario autenticado
        $user = Auth::guard('api')->user();
        
        // Obtener el shipping order
        $shippingOrderId = $request->route('shippingOrder_id');
        $shippingOrder = ShippingOrder::find($shippingOrderId);

        if (!$shippingOrder) {
            return response()->json(['message' => 'Shipping Order no encontrado'], 404);
        }

        // Verificar si el usuario tiene los roles 'worker' o 'admin'
        if ($user->hasAnyRole(['worker', 'admin'])) {
            return $next($request);
        }

        // Verificar si el usuario es un cliente y el shipping order le pertenece
        if ($user->hasRole('client') && $shippingOrder->user_id == $user->id) {
            return $next($request);
        }

        return response()->json(['message' => 'No tienes los permisos necesarios para realizar esta acción'], 403);
    }
}
