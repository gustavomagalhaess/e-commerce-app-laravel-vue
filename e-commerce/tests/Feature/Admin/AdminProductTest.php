<?php

namespace Tests\Feature\Admin;

use App\Domains\Catalog\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_all_products(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Product::factory()->count(5)->create();

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/products')
            ->assertOk()
            ->assertJsonCount(5, 'data');
    }

    public function test_admin_can_delete_any_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();

        $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/v1/admin/products/{$product->id}")
            ->assertNoContent();
    }

    public function test_customer_cannot_access_admin_products(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer, 'sanctum')
            ->getJson('/api/v1/admin/products')
            ->assertForbidden();
    }
}
