<?php

namespace Tests\Feature\Cart;

use App\Domains\Cart\Models\CartItem;
use App\Domains\Catalog\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_empty_cart(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/cart')
            ->assertOk()
            ->assertJsonStructure(['data', 'total', 'item_count']);
    }

    public function test_user_can_add_item_to_cart(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create(['price' => 50.00]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart/items', ['product_id' => $product->id, 'quantity' => 2])
            ->assertStatus(201)
            ->assertJsonFragment(['quantity' => 2]);
    }

    public function test_adding_same_product_increments_quantity(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        CartItem::factory()->create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 1]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart/items', ['product_id' => $product->id, 'quantity' => 3])
            ->assertStatus(201)
            ->assertJsonFragment(['quantity' => 4]);
    }

    public function test_user_cannot_add_own_product_to_cart(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create(['seller_id' => $user->id]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart/items', ['product_id' => $product->id, 'quantity' => 1])
            ->assertStatus(422)
            ->assertJsonFragment(['message' => 'You cannot add your own product to the cart.']);
    }

    public function test_user_can_update_cart_item_quantity(): void
    {
        $user = User::factory()->create();
        $cartItem = CartItem::factory()->create(['user_id' => $user->id, 'quantity' => 1]);

        $this->actingAs($user, 'sanctum')
            ->putJson("/api/v1/cart/items/{$cartItem->id}", ['quantity' => 5])
            ->assertOk()
            ->assertJsonFragment(['quantity' => 5]);
    }

    public function test_user_can_remove_a_cart_item(): void
    {
        $user = User::factory()->create();
        $cartItem = CartItem::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/cart/items/{$cartItem->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    }

    public function test_user_can_clear_entire_cart(): void
    {
        $user = User::factory()->create();
        CartItem::factory()->count(3)->create(['user_id' => $user->id]);

        $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/v1/cart')
            ->assertNoContent();

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_cart_requires_authentication(): void
    {
        $this->getJson('/api/v1/cart')->assertUnauthorized();
    }
}
