<?php
namespace App\Services;

use App\Models\Branch;
use App\Models\Item;
use App\Models\Inventory;

class AddItemsToBranchesService
{
    public function addItemsToBranches(Branch $branch)
    {
        $items = Item::all();

        foreach ($items as $item) {
            Inventory::create([
                'branch_id' => $branch->id,
                'item_id' => $item->id,
                'quantity' => 0
            ]);
        }
    }
}