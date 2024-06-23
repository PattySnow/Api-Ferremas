<?php

namespace Database\Factories;

use App\Models\ShippingOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShippingOrderFactory extends Factory
{
    protected $model = ShippingOrder::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'cart_id' => \App\Models\Cart::factory(),
            'subtotal' => $this->faker->randomFloat(2, 10, 100),
            'shipping_cost' => $this->faker->randomFloat(2, 1, 10),
            'total' => $this->faker->randomFloat(2, 11, 110),
            'status' => $this->faker->randomElement(['pending', 'shipped', 'delivered']),
        ];
    }
}
