<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Branch;
use App\Models\Item;

class AddItemWithoutStockService
{
    public function AddItemWithoutStock ($name, $description, $price, $category_id)
    {
        $item = Item::create([
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'category_id' => $category_id
        ]);

        if (!$item) {
            return null;
        }

        // Establecer stock 0 para el nuevo producto en todas las sucursales
        $branches = Branch::all();
        foreach ($branches as $branch) {
            Inventory::create([
                'branch_id' => $branch->id,
                'item_id' => $item->id,
                'quantity' => 0
            ]);
        }

        return $item;
    }

 
}
