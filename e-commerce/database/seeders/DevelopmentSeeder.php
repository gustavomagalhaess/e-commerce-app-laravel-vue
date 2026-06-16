<?php

namespace Database\Seeders;

use App\Domains\Catalog\Models\Category;
use App\Domains\Catalog\Models\Product;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (app()->isProduction()) {
            throw new \RuntimeException('DevelopmentSeeder cannot run in production.');
        }

        $categories = Category::factory(8)->create();

        $users = User::factory(20)->create();

        $allProducts = collect();

        foreach ($users as $user) {
            $products = Product::factory(10)->create(['seller_id' => $user->id]);

            $products->each(
                fn (Product $product) => $product->categories()->attach(
                    $categories->random(rand(1, 3))->pluck('id')
                )
            );

            $allProducts = $allProducts->concat($products);
        }

        foreach ($users as $user) {
            for ($i = 0; $i < 10; $i++) {
                $order = Order::create([
                    'buyer_id'       => $user->id,
                    'payment_method' => fake()->randomElement(['credit_card', 'pix', 'boleto']),
                    'status'         => fake()->randomElement(['pending', 'paid', 'cancelled', 'failed']),
                    'total'          => 0,
                ]);

                $pickedProducts = $allProducts->random(rand(1, 3));
                $total = 0;

                foreach ($pickedProducts as $product) {
                    $quantity = rand(1, 5);
                    OrderItem::create([
                        'order_id'          => $order->id,
                        'product_id'        => $product->id,
                        'seller_id'         => $product->seller_id,
                        'quantity'          => $quantity,
                        'price_at_purchase' => $product->price,
                    ]);
                    $total += $quantity * $product->price;
                }

                $order->update(['total' => round($total, 2)]);
            }
        }
    }
}
