<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PasswordExpirationTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_expired_page_redirects_guests_to_login()
    {
        $this->get(route('password.expired'))
            ->assertRedirect(route('login'));
    }

    public function test_password_expired_page_can_be_rendered()
    {
        $user = User::factory()->create(['password_expires_at' => now()->subDay()]);

        $this->actingAs($user)
            ->get(route('password.expired'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('auth/password-expired'));
    }

    public function test_user_with_valid_password_can_access_the_app()
    {
        $user = User::factory()->create(['password_expires_at' => now()->addDay()]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_user_with_expired_password_is_redirected_from_the_app()
    {
        $user = User::factory()->create(['password_expires_at' => now()->subDay()]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('password.expired'));
    }

    public function test_user_with_expired_password_is_redirected_from_settings()
    {
        $user = User::factory()->create(['password_expires_at' => now()->subDay()]);

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertRedirect(route('password.expired'));
    }

    public function test_administrator_with_expired_password_is_redirected_from_administration()
    {
        $user = User::factory()->administrator()->create(['password_expires_at' => now()->subDay()]);

        $this->actingAs($user)
            ->get(route('administration'))
            ->assertRedirect(route('password.expired'));
    }

    public function test_user_with_expired_password_cannot_use_settings_password_route()
    {
        $user = User::factory()->create(['password_expires_at' => now()->subDay()]);

        $this->actingAs($user)
            ->put(route('user-password.update'), [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect(route('password.expired'));

        $this->assertFalse($user->refresh()->hasValidPassword());
    }

    public function test_new_password_on_expired_page_must_be_different_from_current_password()
    {
        $user = User::factory()->create(['password_expires_at' => now()->subDay()]);

        $this->actingAs($user)
            ->from(route('password.expired'))
            ->put(route('password.expired.update'), [
                'current_password' => 'password',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertSessionHasErrors('password')
            ->assertRedirect(route('password.expired'));
    }

    public function test_user_with_expired_password_can_update_password()
    {
        $user = User::factory()->create(['password_expires_at' => now()->subDay()]);

        $this->actingAs($user)
            ->from(route('password.expired'))
            ->put(route('password.expired.update'), [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $this->assertTrue($user->refresh()->hasValidPassword());
    }

    public function test_user_is_redirected_to_intended_page_after_updating_expired_password()
    {
        $user = User::factory()->create(['password_expires_at' => now()->subDay()]);

        $this->actingAs($user)
            ->get(route('player.index'))
            ->assertRedirect(route('password.expired'));

        $this->from(route('password.expired'))
            ->put(route('password.expired.update'), [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect(route('player.index'));
    }
}
