<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'cart_id' => Cart::factory(),
            'total' => $this->faker->numberBetween(1000, 9000),
            'status' => 'Pending',
        ];
    }
}
