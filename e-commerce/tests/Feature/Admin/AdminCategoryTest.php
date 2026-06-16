<?php

namespace Tests\Feature\Admin;

use App\Domains\Catalog\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/admin/categories', ['name' => 'Electronics', 'slug' => 'electronics'])
            ->assertStatus(201)
            ->assertJsonFragment(['name' => 'Electronics']);
    }

    public function test_customer_cannot_create_category(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer, 'sanctum')
            ->postJson('/api/v1/admin/categories', ['name' => 'Test', 'slug' => 'test'])
            ->assertForbidden();
    }

    public function test_admin_can_update_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        $this->actingAs($admin, 'sanctum')
            ->putJson("/api/v1/admin/categories/{$category->id}", ['name' => 'Updated', 'slug' => 'updated'])
            ->assertOk()
            ->assertJsonFragment(['name' => 'Updated']);
    }

    public function test_admin_can_delete_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/v1/admin/categories/{$category->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
