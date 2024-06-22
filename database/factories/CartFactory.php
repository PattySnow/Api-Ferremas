<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'branch_id' => Branch::factory(),
            'total' => 0,
            'status' => 'Pending',
            'delivery_type' => 'Pick Up'
           
        ];
    }
}
