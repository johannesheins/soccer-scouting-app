<?php

namespace Tests\Feature\Player;

use App\Enums\AuditEvent;
use App\Enums\FootEnum;
use App\Enums\Permission\PlayerPermissions;
use App\Models\AuditLog;
use App\Models\Club;
use App\Models\Player;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Inertia;
use Tests\TestCase;

class PlayerControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUserWithRight([
            PlayerPermissions::Search,
            PlayerPermissions::Create,
            PlayerPermissions::View,
            PlayerPermissions::Edit,
            PlayerPermissions::Destroy,
            PlayerPermissions::ViewHistory,
        ]);
    }

    #region create
    public function test_create_shows_player_create_view(): void
    {
        $clubs = Club::factory(10)->create();
        $positions = Position::factory(8)->create();

        $response = $this->actingAs($this->user)
            ->get(route('player.create'));

        $firstClub = $clubs->sortBy('clubname')->first();
        $firstPosition = $positions->sortBy('id')->first();

        $response->assertInertia(
            fn ($page) => $page
            ->component('player/player-create')
            ->has('clubs', 10)
            ->where('clubs.0.id', $firstClub->id)
            ->where('clubs.0.clubname', $firstClub->clubname)
            ->has('positions', 8)
            ->where('positions.0.id', $firstPosition->id)
            ->where('positions.0.position_code', $firstPosition->position_code)
        );
        $this->assertRights(PlayerPermissions::Create, 'player.create');
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
                'height' => 180,
                'strong_foot' => FootEnum::LEFT->value,
                'club_id' => $clubs->id,
                'position_ids' => $positions->pluck('id')->toArray(),
            ]);

        $response->assertRedirect(route('player.search'));
        $this->assertDatabaseHas('players', [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'year_of_birth' => 1999,
            'height' => 180,
            'strong_foot' => FootEnum::LEFT->value,
        ]);
        $player = Player::where('firstname', 'John')->first();
        $positions->each(fn ($pos) => $this->assertDatabaseHas('player_positions', ['player_id' => $player->id, 'position_id' => $pos->id]));

        $this->assertRights(PlayerPermissions::Create, 'player.create');
    }

    public function test_store_logs_positions(): void
    {
        $positions = Position::factory(2)->create();

        $this->actingAs($this->user)
            ->post(route('player.store'), [
                'firstname' => 'John',
                'position_ids' => $positions->pluck('id')->toArray(),
            ]);

        $player = Player::where('firstname', 'John')->sole();
        $log = $player->auditLogs()->sole();
        $this->assertSame(AuditEvent::CREATED, $log->event);
        $this->assertSame(Player::class, $log->auditable_type);
        $this->assertNull($log->old_values);
        $this->assertSame('John', $log->new_values['firstname']);
        $this->assertSame($positions->pluck('id')->sort()->values()->all(), $log->new_values['positions']);
        $this->assertSame($this->user->id, $log->user_id);
    }

    public function test_store_guest_redirect_login(): void
    {
        $club = Club::factory()->create();
        $positions = Position::factory(2)->create();

        $response = $this->post(route('player.store'), [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'year_of_birth' => 1999,
            'height' => 180,
            'strong_foot' => FootEnum::LEFT->value,
            'club_id' => $club->id,
            'position_ids' => $positions->pluck('id')->toArray(),
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_store_succeeds_with_no_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('player.store'), []);

        $response->assertValid();
        $response->assertRedirect(route('player.search'));
        $this->assertDatabaseHas('players', ['firstname' => null, 'club_id' => null]);
    }

    public function test_store_validates_club_exists(): void
    {
        $positions = Position::factory(2)->create();

        $response = $this->actingAs($this->user)
            ->post(route('player.store'), [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'year_of_birth' => 1999,
                'height' => 180,
                'strong_foot' => FootEnum::LEFT->value,
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
                'height' => 180,
                'strong_foot' => FootEnum::LEFT->value,
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

    public function test_store_validates_year_of_birth_not_has_right_format(): void
    {
        $club = Club::factory()->create();
        $position = Position::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('player.store'), [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'year_of_birth' => 'not-an-integer',
                'height' => 180,
                'strong_foot' => FootEnum::LEFT->value,
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
                'height' => 180,
                'strong_foot' => FootEnum::LEFT->value,
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
                'height' => 180,
                'strong_foot' => FootEnum::LEFT->value,
                'club_id' => $club->id,
                'position_ids' => [$position->id],
            ]);

        $response->assertInvalid(['lastname']);
    }

    public function test_store_validates_strong_foot_is_valid_enum_value(): void
    {
        $club = Club::factory()->create();
        $position = Position::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('player.store'), [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'year_of_birth' => 1999,
                'height' => 180,
                'strong_foot' => 'not-a-foot',
                'club_id' => $club->id,
                'position_ids' => [$position->id],
            ]);

        $response->assertInvalid(['strong_foot']);
    }

    public function test_store_validates_height_is_integer(): void
    {
        $club = Club::factory()->create();
        $position = Position::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('player.store'), [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'year_of_birth' => 1999,
                'height' => 'not-a-number',
                'strong_foot' => FootEnum::LEFT->value,
                'club_id' => $club->id,
                'position_ids' => [$position->id],
            ]);

        $response->assertInvalid(['height']);
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
            'height' => 180,
            'strong_foot' => FootEnum::LEFT->value,
            'club_id' => $club->id,
        ]);
        $player->positions()->attach($positions);

        $response = $this->actingAs($this->user)
            ->get(route('player.show', $player));

        $response->assertInertia(
            fn ($page) => $page
            ->component('player/player-search')
            ->where('modal.component', 'player/player-show')
            ->where('modal.props.player.id', $player->id)
            ->where('modal.props.player.height', 180)
            ->where('modal.props.player.strong_foot', FootEnum::LEFT->value)
        );

        $this->assertRights(PlayerPermissions::View, ['player.show', $player->id]);
    }

    public function test_show_returns_404_for_nonexistent_player(): void
    {
        $this->actingAs($this->user)
            ->get(route('player.show', 999))
            ->assertNotFound();
    }
    #endregion

    #region history
    public function test_history_guest_redirect_login(): void
    {
        $player = Player::factory()->create();

        $this->get(route('player.history', $player))
            ->assertRedirect(route('login'));
    }

    public function test_history_renders_modal_with_versions(): void
    {
        $player = Player::factory()->create();
        $player->saveLog(AuditEvent::CREATED, null, $player->createSnapshot());

        $response = $this->actingAs($this->user)
            ->get(route('player.history', $player));

        $response->assertInertia(
            fn ($page) => $page
            ->component('player/player-search')
            ->where('modal.component', 'player/player-view-history')
            ->where('modal.props.player.id', $player->id)
            ->has('modal.props.versions', 1)
            ->where('modal.props.versions.0.event', AuditEvent::CREATED->value)
            ->where('modal.props.versions.0.player.id', $player->id)
            ->has('modal.props.versions.0.player.club')
            ->has('modal.props.versions.0.player.positions')
        );
    }

    public function test_history_requires_the_view_history_and_view_right(): void
    {
        $player = Player::factory()->create();

        $this->actingAs($this->createUserWithRight([PlayerPermissions::ViewHistory, PlayerPermissions::View]))
            ->get(route('player.history', $player))
            ->assertOk();

        $this->actingAs(User::factory()->administrator()->create())
            ->get(route('player.history', $player))
            ->assertOk();

        $this->actingAs(User::factory()->create())
            ->get(route('player.history', $player))
            ->assertForbidden();
    }

    public function test_history_is_forbidden_with_view_history_but_without_view(): void
    {
        $player = Player::factory()->create();

        $this->actingAs($this->createUserWithRight([PlayerPermissions::ViewHistory]))
            ->get(route('player.history', $player))
            ->assertForbidden();
    }

    public function test_history_is_forbidden_with_view_but_without_view_history(): void
    {
        $player = Player::factory()->create();

        $this->actingAs($this->createUserWithRight([PlayerPermissions::View]))
            ->get(route('player.history', $player))
            ->assertForbidden();
    }

    public function test_history_renders_historical_club_and_positions(): void
    {
        $oldClub = Club::factory()->create();
        $newClub = Club::factory()->create();
        [$oldPosition, $newPosition] = Position::factory(2)->create()->all();
        $player = Player::factory()->create(['club_id' => $oldClub->id, 'height' => 170]);
        $player->positions()->attach($oldPosition);
        $player->saveLog(AuditEvent::CREATED, null, $player->createSnapshot());

        $old = $player->createSnapshot();
        $player->update(['club_id' => $newClub->id, 'height' => 180]);
        $player->positions()->sync([$newPosition->id]);
        $player->saveLog(AuditEvent::UPDATED, $old, $player->createSnapshot());

        $response = $this->actingAs($this->user)
            ->get(route('player.history', $player));

        $response->assertInertia(
            fn ($page) => $page
            ->has('modal.props.versions', 2)
            ->where('modal.props.versions.0.event', AuditEvent::CREATED->value)
            ->where('modal.props.versions.0.player.height', 170)
            ->where('modal.props.versions.0.player.club.id', $oldClub->id)
            ->has('modal.props.versions.0.player.positions', 1)
            ->where('modal.props.versions.0.player.positions.0.id', $oldPosition->id)
            ->where('modal.props.versions.1.event', AuditEvent::UPDATED->value)
            ->where('modal.props.versions.1.player.height', 180)
            ->where('modal.props.versions.1.player.club.id', $newClub->id)
            ->has('modal.props.versions.1.player.positions', 1)
            ->where('modal.props.versions.1.player.positions.0.id', $newPosition->id)
        );
    }

    public function test_history_does_not_write_to_the_database(): void
    {
        $player = Player::factory()->create();
        $player->positions()->attach(Position::factory(2)->create());
        $player->saveLog(AuditEvent::CREATED, null, $player->createSnapshot());

        $this->actingAs($this->user)
            ->get(route('player.history', $player))
            ->assertOk();

        $this->assertDatabaseCount('positions', 2);
        $this->assertDatabaseCount('player_positions', 2);
        $this->assertDatabaseCount('audit_logs', 1);
    }

    public function test_history_returns_404_for_nonexistent_player(): void
    {
        $this->actingAs($this->user)
            ->get(route('player.history', 999))
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
            'height' => 180,
            'strong_foot' => FootEnum::LEFT->value,
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
            ->where('player.height', 180)
            ->where('player.strong_foot', FootEnum::LEFT->value)
            ->where('player.club_id', $clubs->id)
            ->has('player.positions', 2)
        );

        $this->assertRights(PlayerPermissions::Edit, ['player.edit', $player->id]);
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
            ->from(route('player.search'))
            ->put(route('player.update', $player->id), [
                'firstname'     => 'Jacob',
                'lastname'      => $player->lastname,
                'year_of_birth' => $player->year_of_birth,
                'height'        => 190,
                'strong_foot'   => FootEnum::RIGHT->value,
                'club_id'       => $club->id,
                'position_ids'  => [$newPosition->id],
            ]);

        $response->assertRedirect(route('player.search'));
        $this->assertDatabaseHas('players', ['id' => $player->id, 'firstname' => 'Jacob', 'height' => 190, 'strong_foot' => FootEnum::RIGHT->value]);
        $this->assertDatabaseHas('player_positions', ['player_id' => $player->id, 'position_id' => $newPosition->id]);
        $oldPosition->each(fn ($pos) => $this->assertDatabaseMissing('player_positions', ['player_id' => $player->id, 'position_id' => $pos->id]));

        $this->assertRights(PlayerPermissions::Edit, ['player.update', $player]);
    }

    public function test_update_logs_positions(): void
    {
        $oldPosition = Position::factory()->create();
        $newPosition = Position::factory()->create();
        $player = Player::factory()->create();
        $player->positions()->attach($oldPosition->id);

        $this->actingAs($this->user)
            ->put(route('player.update', $player), [
                'position_ids' => [$newPosition->id],
            ]);

        $log = $player->auditLogs()->where('event', AuditEvent::UPDATED)->sole();
        $this->assertSame([$oldPosition->id], $log->old_values['positions']);
        $this->assertSame([$newPosition->id], $log->new_values['positions']);
        $this->assertSame($this->user->id, $log->user_id);
    }

    public function test_update_guest_redirect_login(): void
    {
        $player = Player::factory()->create();

        $this->put(route('player.update', $player))
            ->assertRedirect(route('login'));
    }

    public function test_update_flash_reaches_history_modal_after_restore(): void
    {
        $player = Player::factory()->create();

        $this->actingAs($this->user)
            ->from(route('player.history', $player))
            ->put(route('player.update', $player), ['firstname' => 'Jacob'])
            ->assertRedirect(route('player.history', $player));

        $this->withHeaders(['X-Inertia' => 'true', 'X-Inertia-Version' => Inertia::getVersion()])
            ->get(route('player.history', $player))
            ->assertHeader('X-Inertia-Modal')
            ->assertJsonPath('flash.toast.message', __('Player was updated.'));
    }

    public function test_update_succeeds_with_no_fields(): void
    {
        $player = Player::factory()->create();

        $response = $this->actingAs($this->user)
            ->from(route('player.search'))
            ->put(route('player.update', $player), []);

        $response->assertValid();
        $response->assertRedirect(route('player.search'));
        $this->assertDatabaseHas('players', ['id' => $player->id, 'firstname' => $player->firstname, 'club_id' => $player->club_id]);
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
                'height' => 180,
                'strong_foot' => FootEnum::LEFT->value,
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
                'height' => 180,
                'strong_foot' => FootEnum::LEFT->value,
                'club_id' => $club->id,
                'position_ids' => [999],
            ]);

        $response->assertInvalid(['position_ids.0']);
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
                'height' => 180,
                'strong_foot' => FootEnum::LEFT->value,
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
                'height' => 180,
                'strong_foot' => FootEnum::LEFT->value,
                'club_id' => $club->id,
                'position_ids' => [$position->id],
            ]);

        $response->assertInvalid(['lastname']);
    }

    public function test_update_validates_strong_foot_is_valid_enum_value(): void
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
                'height' => 180,
                'strong_foot' => 'not-a-foot',
                'club_id' => $club->id,
                'position_ids' => [$position->id],
            ]);

        $response->assertInvalid(['strong_foot']);
    }

    public function test_update_validates_height_is_integer(): void
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
                'height' => 'not-a-number',
                'strong_foot' => FootEnum::LEFT->value,
                'club_id' => $club->id,
                'position_ids' => [$position->id],
            ]);

        $response->assertInvalid(['height']);
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
                'height' => 180,
                'strong_foot' => FootEnum::LEFT->value,
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

        $this->assertRights(PlayerPermissions::Destroy, ['player.destroy', $player->id]);

        $response = $this->actingAs($this->user)
            ->from(route('player.search'))
            ->delete(route('player.destroy', $player->id));

        $response->assertRedirect(route('player.search'));
        $this->assertDatabaseMissing('players', ['id' => $player->id]);
        $this->assertDatabaseMissing('player_positions', ['player_id' => $player->id]);
    }

    public function test_destroy_logs_positions(): void
    {
        $positions = Position::factory(2)->create();
        $player = Player::factory()->create(['firstname' => 'John']);
        $player->positions()->attach($positions->pluck('id'));

        $this->actingAs($this->user)
            ->delete(route('player.destroy', $player));

        $log = AuditLog::query()
            ->where('auditable_id', $player->id)
            ->where('event', AuditEvent::DELETED)
            ->sole();
        $this->assertSame('John', $log->old_values['firstname']);
        $this->assertSame($positions->pluck('id')->sort()->values()->all(), $log->old_values['positions']);
        $this->assertNull($log->new_values);
        $this->assertSame($this->user->id, $log->user_id);
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
