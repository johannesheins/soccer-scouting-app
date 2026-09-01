<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_dashboard_shows_the_users_player_quick_search_years_of_birth()
    {
        $user = User::factory()->create();
        $user->playerQuickSearchYearsOfBirth()->createMany([
            ['year_of_birth' => 2010],
            ['year_of_birth' => 2012],
        ]);

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertInertia(
            fn (Assert $page) => $page
            ->component('dashboard')
            ->has('playerQuickSearchUserYears', 2)
        );
    }
}
