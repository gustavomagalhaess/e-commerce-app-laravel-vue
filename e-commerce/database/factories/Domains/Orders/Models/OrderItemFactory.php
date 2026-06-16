<?php

namespace Database\Factories\Domains\Orders\Models;

use App\Domains\Catalog\Models\Product;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'seller_id' => User::factory(),
            'quantity' => fake()->numberBetween(1, 5),
            'price_at_purchase' => fake()->randomFloat(2, 5, 200),
        ];
    }
}
