<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\Branch;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    public function definition()
    {
        return [
            'branch_id' => Branch::factory(),
            'item_id' => Item::factory(),
            'quantity' => $this->faker->numberBetween(1, 100),
        ];
    }
}
