<?php

namespace Database\Factories\Domains\Orders\Models;

use App\Domains\Orders\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'buyer_id' => User::factory(),
            'payment_method' => fake()->randomElement(['credit_card', 'pix', 'boleto']),
            'status' => 'pending',
            'total' => fake()->randomFloat(2, 10, 500),
        ];
    }
}
