<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShippingOrder;

class ShippingOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shippingOrders = ShippingOrder::all();
        return response()->json($shippingOrders);
    }

    public function createFromCart($cart, $subtotal, $shipping_cost, $total)
    {
        return ShippingOrder::create([
            'user_id' => $cart->user_id,
            'cart_id' => $cart->id,
            'subtotal' => $subtotal,
            'shipping_cost' => $shipping_cost,
            'total' => $total,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shippingOrder = ShippingOrder::findOrFail($id);
        return response()->json($shippingOrder);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'status' => 'required|string|max:255',
        ]);

        $shippingOrder = ShippingOrder::findOrFail($id);
        $shippingOrder->update($validatedData);
        return response()->json($shippingOrder);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $shippingOrder = ShippingOrder::findOrFail($id);
        $shippingOrder->delete();

        return response()->json(['message' => 'Orden de envío eliminada con éxito'], 200);
    }
}
