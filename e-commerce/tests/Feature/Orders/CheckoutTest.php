<?php

namespace Tests\Feature\Orders;

use App\Domains\Cart\Models\CartItem;
use App\Domains\Catalog\Models\Product;
use App\Domains\Orders\Jobs\ProcessOrder;
use App\Domains\Orders\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_dispatches_job_and_returns_202(): void
    {
        Bus::fake();

        $buyer = User::factory()->create(['role' => 'customer']);
        $seller = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create(['seller_id' => $seller->id, 'price' => 100.00]);
        CartItem::factory()->create(['user_id' => $buyer->id, 'product_id' => $product->id, 'quantity' => 2]);

        $response = $this->actingAs($buyer, 'sanctum')
            ->postJson('/api/v1/orders', ['payment_method' => 'pix']);

        $response->assertStatus(202)
            ->assertJsonStructure(['data' => ['id', 'status', 'payment_method']])
            ->assertJsonPath('data.status', 'pending');

        Bus::assertDispatched(ProcessOrder::class);

        $this->assertDatabaseHas('orders', [
            'buyer_id' => $buyer->id,
            'status' => 'pending',
            'payment_method' => 'pix',
        ]);
    }

    public function test_checkout_fails_with_empty_cart(): void
    {
        $buyer = User::factory()->create();

        $this->actingAs($buyer, 'sanctum')
            ->postJson('/api/v1/orders', ['payment_method' => 'credit_card'])
            ->assertStatus(422)
            ->assertJsonFragment(['message' => 'Your cart is empty.']);
    }

    public function test_process_order_job_creates_order_items_and_clears_cart(): void
    {
        $buyer = User::factory()->create(['role' => 'customer']);
        $seller = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create(['seller_id' => $seller->id, 'price' => 99.90]);
        CartItem::factory()->create(['user_id' => $buyer->id, 'product_id' => $product->id, 'quantity' => 3]);

        $order = Order::create([
            'buyer_id' => $buyer->id,
            'payment_method' => 'boleto',
            'status' => 'pending',
            'total' => 0,
        ]);

        (new \App\Domains\Orders\Jobs\ProcessOrder($order->id, $buyer->id))
            ->handle(app(\App\Domains\Orders\Services\OrderService::class));

        $order->refresh();

        $this->assertEquals('paid', $order->status);
        $this->assertEquals('299.70', $order->total);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'seller_id' => $seller->id,
            'quantity' => 3,
            'price_at_purchase' => '99.90',
        ]);
        $this->assertDatabaseCount('cart_items', 0);
    }
}
