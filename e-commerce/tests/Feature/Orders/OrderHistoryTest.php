<?php

namespace Tests\Feature\Orders;

use App\Domains\Catalog\Models\Product;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_list_own_orders(): void
    {
        $buyer = User::factory()->create();
        Order::factory()->count(3)->create(['buyer_id' => $buyer->id]);
        Order::factory()->count(2)->create(); // other buyers

        $this->actingAs($buyer, 'sanctum')
            ->getJson('/api/v1/orders')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_buyer_can_view_order_detail(): void
    {
        $buyer = User::factory()->create();
        $order = Order::factory()->create(['buyer_id' => $buyer->id]);

        $this->actingAs($buyer, 'sanctum')
            ->getJson("/api/v1/orders/{$order->id}")
            ->assertOk()
            ->assertJsonStructure(['data' => ['id', 'status', 'payment_method', 'total', 'items']]);
    }

    public function test_seller_can_view_their_sales(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $product = Product::factory()->create(['seller_id' => $seller->id]);
        $order = Order::factory()->create(['buyer_id' => $buyer->id, 'status' => 'paid']);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'seller_id' => $seller->id,
            'quantity' => 1,
            'price_at_purchase' => 50.00,
        ]);

        $this->actingAs($seller, 'sanctum')
            ->getJson('/api/v1/my-sales')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }
}
