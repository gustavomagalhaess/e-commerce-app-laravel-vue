<?php

namespace Tests\Feature\Catalog;

use App\Domains\Catalog\Models\Category;
use App\Domains\Catalog\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductListTest extends TestCase
{
    use RefreshDatabase;

    public function test_anyone_can_list_products_paginated(): void
    {
        Product::factory()->count(15)->create();

        $response = $this->getJson('/api/v1/products');

        $response->assertOk()
            ->assertJsonStructure(['data', 'meta' => ['current_page', 'total', 'per_page']])
            ->assertJsonCount(12, 'data');
    }

    public function test_products_can_be_searched_by_name(): void
    {
        Product::factory()->create(['name' => 'Blue Widget']);
        Product::factory()->create(['name' => 'Red Gadget']);

        $this->getJson('/api/v1/products?search=Blue')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Blue Widget']);
    }

    public function test_products_can_be_filtered_by_category(): void
    {
        $electronics = Category::factory()->create();
        $books = Category::factory()->create();
        $p1 = Product::factory()->create();
        $p2 = Product::factory()->create();
        $p1->categories()->attach($electronics);
        $p2->categories()->attach($books);

        $this->getJson("/api/v1/products?category[]={$electronics->id}")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['id' => $p1->id]);
    }

    public function test_anyone_can_view_a_single_product(): void
    {
        $product = Product::factory()->create();

        $this->getJson("/api/v1/products/{$product->id}")
            ->assertOk()
            ->assertJsonFragment(['id' => $product->id])
            ->assertJsonStructure(['data' => ['id', 'name', 'description', 'price', 'categories', 'seller']]);
    }
}
