<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Inventory;

class CheckStockService
{
    public function CheckStockService(array $items, $branch_id)
    {
        $errores = [];

        foreach ($items as $itemdata) {
            $item_id = $itemdata['item_id'];
            $quantity = $itemdata['quantity'];

            $item = Item::find($item_id);

            if (!$item) {
                $error[] = "El producto $item_id no existe";
                continue;
            }

            // Obtener el inventario del producto en la sucursal
            $inventory = Inventory::where('item_id', $item_id)
                                    ->where('branch_id', $branch_id)
                                    ->first();

            if (!$inventory || $inventory->quantity < $quantity) {
                $error[] = "Stock insuficiente para el producto $item_id";
            }
        }

        if (!empty($error)) {
            return response()->json(['message' => $error], 400);
        }

        return null; // Retorna null si todos los productos tienen stock suficiente
    }
}
