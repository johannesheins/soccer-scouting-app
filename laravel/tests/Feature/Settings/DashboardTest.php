<?php

namespace Tests\Feature\Settings;

use App\Models\Club;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_page_is_displayed()
    {
        $user = User::factory()->create();
        Club::factory(3)->create();

        $response = $this->actingAs($user)
            ->get(route('settings.dashboard.index'));

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page) => $page
            ->component('settings/dashboard')
            ->has('clubs', 3)
            ->has('playerQuickSearchUserClubs', 0)
        );
    }

    public function test_dashboard_page_shows_the_users_player_quick_search_clubs()
    {
        $user = User::factory()->create();
        $clubs = Club::factory(2)->create();
        $user->playerQuickSearchClubs()->attach($clubs);

        $response = $this->actingAs($user)
            ->get(route('settings.dashboard.index'));

        $response->assertInertia(
            fn (Assert $page) => $page
            ->component('settings/dashboard')
            ->has('playerQuickSearchUserClubs', 2)
        );
    }

    public function test_player_quick_search_clubs_can_be_updated()
    {
        $user = User::factory()->create();
        $clubs = Club::factory(3)->create();

        $response = $this->actingAs($user)
            ->post(route('settings.dashboard.update-pinned-clubs'), [
                'club_ids' => $clubs->pluck('id')->toArray(),
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('settings.dashboard.index'));

        $clubs->each(fn (Club $club) => $this->assertDatabaseHas('player_quick_search_user_clubs', [
            'user_id' => $user->id,
            'club_id' => $club->id,
        ]));
    }

    public function test_player_quick_search_clubs_replaces_the_previous_selection()
    {
        $user = User::factory()->create();
        $oldClub = Club::factory()->create();
        $newClub = Club::factory()->create();
        $user->playerQuickSearchClubs()->attach($oldClub);

        $this->actingAs($user)
            ->post(route('settings.dashboard.update-pinned-clubs'), [
                'club_ids' => [$newClub->id],
            ]);

        $this->assertDatabaseMissing('player_quick_search_user_clubs', ['user_id' => $user->id, 'club_id' => $oldClub->id]);
        $this->assertDatabaseHas('player_quick_search_user_clubs', ['user_id' => $user->id, 'club_id' => $newClub->id]);
    }

    public function test_player_quick_search_clubs_can_be_cleared()
    {
        $user = User::factory()->create();
        $club = Club::factory()->create();
        $user->playerQuickSearchClubs()->attach($club);

        $response = $this->actingAs($user)
            ->post(route('settings.dashboard.update-pinned-clubs'), [
                'club_ids' => [],
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('player_quick_search_user_clubs', ['user_id' => $user->id, 'club_id' => $club->id]);
    }

    public function test_player_quick_search_clubs_defaults_to_empty_when_field_is_omitted()
    {
        $user = User::factory()->create();
        $club = Club::factory()->create();
        $user->playerQuickSearchClubs()->attach($club);

        $response = $this->actingAs($user)
            ->post(route('settings.dashboard.update-pinned-clubs'), []);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('player_quick_search_user_clubs', ['user_id' => $user->id, 'club_id' => $club->id]);
    }

    public function test_player_quick_search_clubs_does_not_affect_other_users()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $club = Club::factory()->create();
        $otherUser->playerQuickSearchClubs()->attach($club);

        $this->actingAs($user)
            ->post(route('settings.dashboard.update-pinned-clubs'), [
                'club_ids' => [$club->id],
            ]);

        $this->assertDatabaseHas('player_quick_search_user_clubs', ['user_id' => $user->id, 'club_id' => $club->id]);
        $this->assertDatabaseHas('player_quick_search_user_clubs', ['user_id' => $otherUser->id, 'club_id' => $club->id]);
    }

    public function test_player_quick_search_clubs_validates_at_most_three()
    {
        $clubs = Club::factory(4)->create();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('settings.dashboard.update-pinned-clubs'), [
                'club_ids' => $clubs->pluck('id')->toArray(),
            ]);

        $response->assertInvalid(['club_ids']);
    }

    public function test_player_quick_search_clubs_validates_club_exists()
    {
        $response = $this->actingAs(User::factory()->create())
            ->post(route('settings.dashboard.update-pinned-clubs'), [
                'club_ids' => [999],
            ]);

        $response->assertInvalid(['club_ids.0']);
    }

    public function test_dashboard_index_guest_redirect_login()
    {
        $response = $this->get(route('settings.dashboard.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_update_player_quick_search_clubs_guest_redirect_login()
    {
        $club = Club::factory()->create();

        $response = $this->post(route('settings.dashboard.update-pinned-clubs'), [
            'club_ids' => [$club->id],
        ]);

        $response->assertRedirect(route('login'));
    }
}
