<?php

namespace Tests\Feature\Player;

use App\Models\Club;
use App\Models\Player;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #region index
    public function test_index(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('player.index'));

        $response->assertInertia(fn ($page) => $page->component('player/player-index'));
    }

    public function test_index_guest_redirect_login(): void
    {
        $response = $this->get(route('player.index'));
        $response->assertRedirect(route('login'));
    }
    #endregion

    #region store
    public function test_store_creates_player(): void
    {
        $clubs = Club::factory()->create();
        $positions = Position::factory(2)->create();

        $response = $this->actingAs($this->user)
            ->post(route('player.store'), [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'year_of_birth' => 1999,
                'club_id' => $clubs->id,
                'position_ids' => $positions->pluck('id')->toArray(),
            ]);

        $response->assertRedirect(route('player.index'));
        $this->assertDatabaseHas('players', ['firstname' => 'John', 'lastname' => 'Doe', 'year_of_birth' => 1999]);
    }

    public function test_store_guest_redirect_login(): void
    {
        $club = Club::factory()->create();
        $positions = Position::factory(2)->create();

        $response = $this->post(route('player.store'), [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'year_of_birth' => 1999,
            'club_id' => $club->id,
            'position_ids' => $positions->pluck('id')->toArray(),
        ]);

        $response->assertRedirect(route('login'));
    }
    #endregion

    #region edit
    public function test_edit_show_player_update_view(): void
    {
        $clubs = Club::factory()->create();
        $positions = Position::factory(2)->create();
        $player = Player::factory()->create([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'year_of_birth' => 1999,
            'club_id' => $clubs->id,
        ]);
        $player->positions()->attach($positions);

        $response = $this->actingAs($this->user)
            ->get(route('player.edit', $player));

        $response->assertInertia(
            fn ($page) => $page
            ->component('player/player-edit')
            ->where('player.id', $player->id)
            ->where('player.firstname', 'John')
            ->where('player.lastname', 'Doe')
            ->where('player.year_of_birth', 1999)
            ->where('player.club_id', $clubs->id)
            ->has('player.positions', 2)
        );
    }

    public function test_edit_guest_redirect_login(): void
    {
        $player = Player::factory()->create();

        $this->get(route('player.edit', $player))
            ->assertRedirect(route('login'));
    }
    #endregion

    #region update
    public function test_update_changes_player_data(): void
    {
        $club = Club::factory()->create();
        $oldPosition = Position::factory()->create();
        $newPosition = Position::factory()->create();

        $player = Player::factory()->create([
            'firstname' => 'John',
            'club_id'   => $club->id,
        ]);
        $player->positions()->attach($oldPosition->id);

        $response = $this->actingAs($this->user)
            ->put(route('player.update', $player->id), [
                'firstname'     => 'Jacob',
                'lastname'      => $player->lastname,
                'year_of_birth' => $player->year_of_birth,
                'club_id'       => $club->id,
                'position_ids'  => [$newPosition->id],
            ]);

        $response->assertRedirect(route('player.index'));
        $this->assertDatabaseHas('players', ['id' => $player->id, 'firstname' => 'Jacob']);
        $this->assertDatabaseHas('player_positions', ['player_id' => $player->id, 'position_id' => $newPosition->id]);
        $this->assertDatabaseMissing('player_positions', ['player_id' => $player->id, 'position_id' => $oldPosition->id]);
    }

    public function test_update_guest_redirect_login(): void
    {
        $player = Player::factory()->create();

        $this->put(route('player.update', $player))
            ->assertRedirect(route('login'));
    }
    #endregion

    #region destroy
    public function test_destroy_deletes_player(): void
    {
        $club = Club::factory()->create();
        $position = Position::factory()->create();
        $player = Player::factory()->create([
            'firstname' => 'John',
            'club_id'   => $club->id,
        ]);
        $player->positions()->attach($position->id);

        $response = $this->actingAs($this->user)
            ->delete(route('player.destroy', $player->id));

        $response->assertRedirect(route('player.index'));
        $this->assertDatabaseMissing('players', ['id' => $player->id]);
        $this->assertDatabaseMissing('player_positions', ['player_id' => $player->id]);
    }

    public function test_destroy_guest_redirect_login(): void
    {
        $player = Player::factory()->create();

        $response = $this->delete(route('player.destroy', $player));

        $response->assertRedirect(route('login'));
    }
    #endregion

    #region search
    public function test_search_renders_view_with_positions_clubs_and_players(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('player.search'));

        $response->assertInertia(
            fn ($page) => $page
            ->component('player/player-search')
            ->has('positions')
            ->has('clubs')
            ->has('players')
        );
    }

    public function test_search_returns_matching_players(): void
    {
        $club = Club::factory()->create();
        Player::factory()->create(['firstname' => 'John', 'club_id' => $club->id]);
        Player::factory()->create(['firstname' => 'Jane', 'club_id' => $club->id]);

        $response = $this->actingAs($this->user)
            ->get(route('player.search', ['firstname' => 'John']));

        $response->assertInertia(
            fn ($page) => $page
            ->component('player/player-search')
            ->has('players', 1)
            ->where('players.0.firstname', 'John')
        );
    }

    public function test_search_guest_redirect_login(): void
    {
        $response = $this->get(route('player.search'));

        $response->assertRedirect(route('login'));
    }
    #endregion
}
