<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_all_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(4)->create();

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/users')
            ->assertOk()
            ->assertJsonCount(5, 'data'); // 4 + admin
    }

    public function test_admin_can_change_user_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'customer']);

        $this->actingAs($admin, 'sanctum')
            ->putJson("/api/v1/admin/users/{$user->id}/role", ['role' => 'admin'])
            ->assertOk()
            ->assertJsonPath('data.role', 'admin');
    }

    public function test_customer_cannot_list_users(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer, 'sanctum')
            ->getJson('/api/v1/admin/users')
            ->assertForbidden();
    }
}
