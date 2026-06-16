<?php

namespace Database\Factories\Domains\Catalog\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = \App\Domains\Catalog\Models\Product::class;

    public function definition(): array
    {
        return [
            'seller_id' => User::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 5, 500),
            'image_path' => null,
        ];
    }
}
