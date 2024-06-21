<?php
namespace App\Http\Middleware;

use Closure;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBuyOrderAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $orderDetailId = $request->route('id');
        $orderDetail = OrderDetail::find($orderDetailId);

        if (!$orderDetail) {
            return response()->json(['message' => 'OrderDetail not found.'], 404);
        }

        $order = $orderDetail->order;

        if ($user->id === $order->user_id || $user->hasRole(['worker', 'admin'])) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized - No tienes permiso para ver esta orden.'], 403);
    }
}