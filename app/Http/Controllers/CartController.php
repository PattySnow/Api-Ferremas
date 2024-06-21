<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Item;
use App\Models\Branch;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Services\CheckStockService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\ShippingOrderController;

class CartController extends Controller
{
    protected $checkStockService;

    public function __construct(CheckStockService $checkStockService)
    {
        $this->checkStockService = $checkStockService;
    }

    // Método privado para obtener el carrito del usuario autenticado
    private function getCart()
    {
        $user = Auth::user();
        return $user->cart()->where('status', 'Pending')->first();
    }

    private function calculateAmount(Cart $cart)
    {
        $total = $cart->items->sum('pivot.subtotal');
        return $total;
    }

    public function showCart()
    {
        $cart = $this->getCart();

        if (!$cart) {
            return response()->json(['message' => 'El usuario no tiene un carrito.'], 404);
        }

        // Cargar los detalles de los productos del carrito
        $cart->load('items');

        // Preparar la respuesta con los detalles del carrito y productos
        $response = [
            'cart' => [
                'id' => $cart->id,
                'user_id' => $cart->user_id,
                'branch_id' => $cart->branch_id,
                'status' => $cart->status,
                // Otros campos del carrito que desees mostrar
            ],
            'items' => $cart->items->map(function ($item) {
                return [
                    'item_id' => $item->id,
                    'name' => $item->name, // Asegúrate de ajustar el campo según tu estructura de Producto
                    'quantity' => $item->pivot->quantity,
                    // Otros detalles del producto que desees mostrar
                ];
            }),
        ];

        return response()->json($response, 200);
    }


    public function addItems(Request $request, ShippingOrderController $shippingOrderController)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'delivery_type' => 'nullable|string|in:Pick Up,Shipping',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Error en los datos.', 'errors' => $validator->errors()], 422);
        }

        $cart = $this->getCart();

        if (!$cart) {
            $user = Auth::user();
            $cart = Cart::create([
                'user_id' => $user->id,
                'branch_id' => 1,
                'status' => 'Pending',
                'delivery_type' => 'Pick Up',
            ]);
        }

        if ($cart->user_id !== Auth::id()) {
            return response()->json(['message' => 'No tienes permiso para agregar productos a este carrito.'], 403);
        }

        $checkStockService = $this->checkStockService->checkStockService($request->input('items'), $cart->branch_id);
        if ($checkStockService) {
            return $checkStockService;
        }

        $items = $request->input('items');
        $subtotal = 0;

        foreach ($items as $itemsdata) {
            $item_id = $itemsdata['item_id'];
            $quantity = $itemsdata['quantity'];

            $item = Item::find($item_id);

            $unit_price = $item->price;
            $subtotal += $unit_price * $quantity;

            $existingItem = CartItem::where('cart_id', $cart->id)
                ->where('item_id', $item_id)
                ->first();

            if ($existingItem) {
                $existingItem->quantity += $quantity;
                $existingItem->subtotal += $unit_price * $quantity;
                $existingItem->save();
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'item_id' => $item_id,
                    'quantity' => $quantity,
                    'unit_price' => $unit_price,
                    'subtotal' => $unit_price * $quantity,
                ]);
            }
        }

        $total = $subtotal;
        $shipping_cost = 0;

        if ($request->filled('delivery_type') && $request->input('delivery_type') === 'Shipping') {
            $shipping_cost = 3500;
            $total += $shipping_cost;

            $shippingOrderController->createFromCart($cart, $subtotal, $shipping_cost, $total);

            $cart->delivery_type = 'Shipping';
        } else {
            $cart->delivery_type = 'Pick Up';
        }

        $cart->total = $total;
        $cart->save();

        return response()->json(['message' => 'Productos agregados al carrito', 'total' => $total], 200);
    }



    public function removeItem($item_id)
    {
        $cart = $this->getCart();

        if (!$cart) {
            return response()->json(['message' => 'El usuario no tiene un carrito.'], 404);
        }

        if ($cart->user_id !== Auth::id()) {
            return response()->json(['message' => 'No tienes permiso para eliminar productos de este carrito.'], 403);
        }

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('item_id', $item_id)
            ->first();

        if (!$cartItem) {
            return response()->json(['message' => 'El producto no está en el carrito.'], 404);
        }

        $cartItem->delete();

        // Recalcula el total del carrito después de quitar el producto
        $total = $this->calculateAmount($cart);
        $cart->total = $total;
        $cart->save();

        return response()->json(['message' => 'Producto eliminado del carrito', 'total' => $total], 200);
    }

    public function emptyCart()
    {
        // Obtener el carrito del usuario autenticado
        $cart = $this->getCart();

        if (!$cart) {
            return response()->json(['message' => 'El usuario no tiene un carrito para vaciar.'], 404);
        }

        if ($cart->user_id !== Auth::id()) {
            return response()->json(['message' => 'No tienes permiso para vaciar este carrito.'], 403);
        }

        // Eliminar todos los productos asociados al carrito
        $cart->items()->detach();

        // Establecer el total del carrito a 0
        $cart->total = 0;
        $cart->save();

        return response()->json(['message' => 'El carrito ha sido vaciado correctamente.'], 200);
    }


    public function changeBranch($branch_id)
    {
        // Validar que la sucursal existe
        $branch = Branch::find($branch_id);

        if (!$branch) {
            return response()->json(['message' => 'No existe esa sucursal.'], 422);
        }

        $cart = $this->getCart();

        if (!$cart) {
            return response()->json(['message' => 'El usuario no tiene un carrito.'], 404);
        }

        if ($cart->user_id !== Auth::id()) {
            return response()->json(['message' => 'No tienes permiso para cambiar la sucursal de este carrito.'], 403);
        }

        $cart->branch_id = $branch_id;
        $cart->save();

        return response()->json(['message' => "Sucursal actualizada con éxito. Ha cambiado a la sucursal {$branch->name}.", 'carrito' => $cart], 200);
    }
}
