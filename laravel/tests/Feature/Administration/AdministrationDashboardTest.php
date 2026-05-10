<?php

namespace Tests\Feature\Administration;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdministrationDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_as_administrator_can_see_administration_page(): void
    {
        $user = User::factory()->administrator()->create();

        $response = $this->actingAs($user)
            ->get(route('administration.dashboard'));

        $response->assertOk();
        $response->assertInertia(fn($page) => $page
            ->component('administration/dashboard'));
    }

    public function test_users_as_not_administrator_can_not_see_administration_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('administration.dashboard'));

        $response->assertNotFound();
    }
}
