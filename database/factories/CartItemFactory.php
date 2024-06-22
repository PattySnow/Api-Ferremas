<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Item;
use App\Models\CartItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    public function definition()
    {
        return [
            'cart_id' => Cart::factory(),
            'item_id' => Item::factory(),
            'quantity' => $this->faker->numberBetween(1, 10),
            'unit_price' => $this->faker->numberBetween(10, 100),
            'subtotal' => $this->faker->numberBetween(10, 1000),
        ];
    }
}
