<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_their_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/user')
            ->assertOk()
            ->assertJsonFragment(['email' => $user->email]);
    }

    public function test_user_can_update_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->putJson('/api/v1/user/profile', ['name' => 'New Name', 'email' => 'new@example.com'])
            ->assertOk()
            ->assertJsonFragment(['name' => 'New Name']);

        $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
    }

    public function test_user_can_change_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('oldpass123')]);

        $this->actingAs($user, 'sanctum')
            ->putJson('/api/v1/user/password', [
                'current_password' => 'oldpass123',
                'password' => 'newpass456',
                'password_confirmation' => 'newpass456',
            ])
            ->assertNoContent();
    }

    public function test_password_change_fails_with_wrong_current_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('oldpass123')]);

        $this->actingAs($user, 'sanctum')
            ->putJson('/api/v1/user/password', [
                'current_password' => 'wrongpass',
                'password' => 'newpass456',
                'password_confirmation' => 'newpass456',
            ])
            ->assertStatus(422)->assertJsonValidationErrors(['current_password']);
    }
}
