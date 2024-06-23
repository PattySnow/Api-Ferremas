<?php

namespace Database\Factories;

use App\Models\OrderDetail;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderDetailFactory extends Factory
{
    protected $model = OrderDetail::class;

    public function definition()
    {
        return [
            'order_id' => Order::factory(),
            'status' => 'Pending',
            'vci' => $this->faker->word,
            'amount' => $this->faker->numberBetween(1000, 10000),
            'buy_order' => $this->faker->unique()->numerify('ORD#####'),
            'session_id' => $this->faker->unique()->uuid,
            'card_number' => $this->faker->creditCardNumber,
            'accounting_date' => $this->faker->date,
            'transaction_date' => $this->faker->dateTime,
            'authorization_code' => $this->faker->numerify('######'),
            'payment_type_code' => 'VD',
            'response_code' => 0,
            'installments_amount' => $this->faker->numberBetween(1000, 10000),
            'installments_number' => $this->faker->numberBetween(1, 12),
            'balance' => $this->faker->numberBetween(0, 10000),
        ];
    }
}
