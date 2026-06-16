<?php

namespace Tests\Feature\Catalog;

use App\Domains\Catalog\Models\Category;
use App\Domains\Catalog\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_create_product_with_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'customer']);
        $category = Category::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/products', [
                'name' => 'Blue Widget',
                'description' => 'A great blue widget.',
                'price' => '29.99',
                'category_ids' => [$category->id],
                'image' => UploadedFile::fake()->image('widget.jpg'),
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Blue Widget'])
            ->assertJsonPath('data.categories.0.id', $category->id);

        Storage::disk('public')->assertExists('products/' . basename($response->json('data.image_path')));
    }

    public function test_create_product_requires_authentication(): void
    {
        $this->postJson('/api/v1/products', [])->assertUnauthorized();
    }

    public function test_create_product_validates_required_fields(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/products', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'description', 'price', 'category_ids']);
    }

    public function test_customer_can_update_own_product(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create(['seller_id' => $user->id]);
        $category = Category::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/products/{$product->id}", [
                '_method' => 'PUT',
                'name' => 'Updated Name',
                'description' => 'Updated description.',
                'price' => '49.99',
                'category_ids' => [$category->id],
            ])
            ->assertOk()
            ->assertJsonFragment(['name' => 'Updated Name']);
    }

    public function test_customer_cannot_update_another_users_product(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $other = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create(['seller_id' => $other->id]);

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/products/{$product->id}", ['_method' => 'PUT', 'name' => 'x', 'description' => 'y', 'price' => 1, 'category_ids' => []])
            ->assertForbidden();
    }

    public function test_customer_can_delete_own_product(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create(['seller_id' => $user->id]);

        $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/products/{$product->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_admin_can_delete_any_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();

        $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/v1/products/{$product->id}")
            ->assertNoContent();
    }

    public function test_customer_can_list_own_products(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        Product::factory()->count(3)->create(['seller_id' => $user->id]);
        Product::factory()->count(2)->create(); // other sellers

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/my-products')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }
}
