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

    #region create
    public function test_create_shows_player_create_view(): void
    {
        $clubs = Club::factory(10)->create();
        $positions = Position::factory(8)->create();

        $response = $this->actingAs($this->user)
            ->get(route('player.create'));

        $response->assertInertia(fn ($page) => $page
            ->component('player/player-create')
            ->has('clubs', 10)
            ->where('clubs.0.id', $clubs->first()->id)
            ->where('clubs.0.clubname', $clubs->first()->clubname)
            ->has('positions', 8)
            ->where('positions.0.id', $positions->first()->id)
            ->where('positions.0.position_code', $positions->first()->position_code)
        );
    }

    public function test_create_guest_redirect_login(): void
    {
        $response = $this->get(route('player.create'));

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
        $player = Player::where('firstname', 'John')->first();
        $positions->each(fn ($pos) => $this->assertDatabaseHas('player_positions', ['player_id' => $player->id, 'position_id' => $pos->id]));
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

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('player.store'), []);

        $response->assertInvalid(['firstname', 'lastname', 'year_of_birth', 'club_id', 'position_ids']);
    }

    public function test_store_validates_club_exists(): void
    {
        $positions = Position::factory(2)->create();

        $response = $this->actingAs($this->user)
            ->post(route('player.store'), [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'year_of_birth' => 1999,
                'club_id' => 999,
                'position_ids' => $positions->pluck('id')->toArray(),
            ]);

        $response->assertInvalid(['club_id']);
    }

    public function test_store_validates_positions_exist(): void
    {
        $club = Club::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('player.store'), [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'year_of_birth' => 1999,
                'club_id' => $club->id,
                'position_ids' => [999],
            ]);

        $response->assertInvalid(['position_ids.0']);
    }

    public function test_store_validates_year_of_birth_is_integer(): void
    {
        $club = Club::factory()->create();
        $position = Position::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('player.store'), [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'year_of_birth' => 'not-an-integer',
                'club_id' => $club->id,
                'position_ids' => [$position->id],
            ]);

        $response->assertInvalid(['year_of_birth']);
    }

    public function test_store_validates_firstname_max_length(): void
    {
        $club = Club::factory()->create();
        $position = Position::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('player.store'), [
                'firstname' => str_repeat('a', 256),
                'lastname' => 'Doe',
                'year_of_birth' => 1999,
                'club_id' => $club->id,
                'position_ids' => [$position->id],
            ]);

        $response->assertInvalid(['firstname']);
    }

    public function test_store_validates_lastname_max_length(): void
    {
        $club = Club::factory()->create();
        $position = Position::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('player.store'), [
                'firstname' => 'John',
                'lastname' => str_repeat('a', 256),
                'year_of_birth' => 1999,
                'club_id' => $club->id,
                'position_ids' => [$position->id],
            ]);

        $response->assertInvalid(['lastname']);
    }
    #endregion

    #region show
    public function test_show_guest_redirect_login(): void
    {
        $player = Player::factory()->create();

        $this->get(route('player.show', $player))
            ->assertRedirect(route('login'));
    }

    public function test_show_renders_modal(): void
    {
        $club = Club::factory()->create();
        $positions = Position::factory(2)->create();
        $player = Player::factory()->create([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'year_of_birth' => 1999,
            'club_id' => $club->id,
        ]);
        $player->positions()->attach($positions);

        $response = $this->actingAs($this->user)
            ->get(route('player.show', $player));

        $response->assertInertia(fn ($page) => $page
            ->component('player/player-index')
            ->where('modal.component', 'player/player-show')
            ->where('modal.props.player.id', $player->id)
        );
    }

    public function test_show_returns_404_for_nonexistent_player(): void
    {
        $this->actingAs($this->user)
            ->get(route('player.show', 999))
            ->assertNotFound();
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

        $response->assertInertia(fn ($page) => $page
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

    public function test_edit_returns_404_for_nonexistent_player(): void
    {
        $this->actingAs($this->user)
            ->get(route('player.edit', 999))
            ->assertNotFound();
    }
    #endregion

    #region update
    public function test_update_changes_player_data(): void
    {
        $club = Club::factory()->create();
        $oldPosition = Position::factory(2)->create();
        $newPosition = Position::factory()->create();

        $player = Player::factory()->create([
            'firstname' => 'John',
            'club_id'   => $club->id,
        ]);
        $player->positions()->attach($oldPosition->pluck('id')->toArray());

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
        $oldPosition->each(fn ($pos) => $this->assertDatabaseMissing('player_positions', ['player_id' => $player->id, 'position_id' => $pos->id]));
    }

    public function test_update_guest_redirect_login(): void
    {
        $player = Player::factory()->create();

        $this->put(route('player.update', $player))
            ->assertRedirect(route('login'));
    }

    public function test_update_validates_required_fields(): void
    {
        $player = Player::factory()->create();

        $response = $this->actingAs($this->user)
            ->put(route('player.update', $player), []);

        $response->assertInvalid(['firstname', 'lastname', 'year_of_birth', 'club_id', 'position_ids']);
    }

    public function test_update_validates_club_exists(): void
    {
        $club = Club::factory()->create();
        $position = Position::factory()->create();
        $player = Player::factory()->create(['club_id' => $club->id]);
        $player->positions()->attach($position->id);

        $response = $this->actingAs($this->user)
            ->put(route('player.update', $player), [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'year_of_birth' => 1999,
                'club_id' => 999,
                'position_ids' => [$position->id],
            ]);

        $response->assertInvalid(['club_id']);
    }

    public function test_update_validates_positions_exist(): void
    {
        $club = Club::factory()->create();
        $player = Player::factory()->create(['club_id' => $club->id]);

        $response = $this->actingAs($this->user)
            ->put(route('player.update', $player), [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'year_of_birth' => 1999,
                'club_id' => $club->id,
                'position_ids' => [999],
            ]);

        $response->assertInvalid(['position_ids.0']);
    }

    public function test_update_validates_year_of_birth_is_integer(): void
    {
        $club = Club::factory()->create();
        $position = Position::factory()->create();
        $player = Player::factory()->create(['club_id' => $club->id]);
        $player->positions()->attach($position->id);

        $response = $this->actingAs($this->user)
            ->put(route('player.update', $player), [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'year_of_birth' => 'not-an-integer',
                'club_id' => $club->id,
                'position_ids' => [$position->id],
            ]);

        $response->assertInvalid(['year_of_birth']);
    }

    public function test_update_validates_firstname_max_length(): void
    {
        $club = Club::factory()->create();
        $position = Position::factory()->create();
        $player = Player::factory()->create(['club_id' => $club->id]);
        $player->positions()->attach($position->id);

        $response = $this->actingAs($this->user)
            ->put(route('player.update', $player), [
                'firstname' => str_repeat('a', 256),
                'lastname' => 'Doe',
                'year_of_birth' => 1999,
                'club_id' => $club->id,
                'position_ids' => [$position->id],
            ]);

        $response->assertInvalid(['firstname']);
    }

    public function test_update_validates_lastname_max_length(): void
    {
        $club = Club::factory()->create();
        $position = Position::factory()->create();
        $player = Player::factory()->create(['club_id' => $club->id]);
        $player->positions()->attach($position->id);

        $response = $this->actingAs($this->user)
            ->put(route('player.update', $player), [
                'firstname' => 'John',
                'lastname' => str_repeat('a', 256),
                'year_of_birth' => 1999,
                'club_id' => $club->id,
                'position_ids' => [$position->id],
            ]);

        $response->assertInvalid(['lastname']);
    }

    public function test_update_returns_404_for_nonexistent_player(): void
    {
        $club = Club::factory()->create();
        $position = Position::factory()->create();

        $this->actingAs($this->user)
            ->put(route('player.update', 999), [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'year_of_birth' => 1999,
                'club_id' => $club->id,
                'position_ids' => [$position->id],
            ])
            ->assertNotFound();
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

    public function test_destroy_returns_404_for_nonexistent_player(): void
    {
        $this->actingAs($this->user)
            ->delete(route('player.destroy', 999))
            ->assertNotFound();
    }
    #endregion

    #region search
    public function test_search_renders_view_with_positions_clubs_and_players(): void
    {
        Player::factory(10)->create();
        Position::factory(10)->create();
        Club::factory(10)->create();

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

    public function test_search_validates_club_exists(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('player.search', ['club_ids' => [999]]));

        $response->assertInvalid(['club_ids.0']);
    }

    public function test_search_validates_positions_exist(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('player.search', ['position_ids' => [999]]));

        $response->assertInvalid(['position_ids.0']);
    }

    public function test_search_validates_years_of_birth_are_integers(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('player.search', ['years_of_birth' => ['not-an-integer']]));

        $response->assertInvalid(['years_of_birth.0']);
    }

    public function test_search_validates_firstname_max_length(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('player.search', ['firstname' => str_repeat('a', 256)]));

        $response->assertInvalid(['firstname']);
    }

    public function test_search_validates_lastname_max_length(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('player.search', ['lastname' => str_repeat('a', 256)]));

        $response->assertInvalid(['lastname']);
    }
    #endregion
}
