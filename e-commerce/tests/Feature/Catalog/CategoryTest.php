<?php

namespace Tests\Feature\Catalog;

use App\Domains\Catalog\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_anyone_can_list_categories(): void
    {
        Category::factory()->count(3)->create();

        $this->getJson('/api/v1/categories')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_categories_response_has_expected_shape(): void
    {
        Category::factory()->create(['name' => 'Electronics', 'slug' => 'electronics']);

        $this->getJson('/api/v1/categories')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Electronics', 'slug' => 'electronics']);
    }
}
