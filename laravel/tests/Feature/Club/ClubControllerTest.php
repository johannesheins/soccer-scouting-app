<?php

namespace Tests\Feature\Club;

use App\Enums\RightEnum;
use App\Models\Club;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClubControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUserWithRight([
            RightEnum::ClubIndex,
            RightEnum::ClubSearch,
            RightEnum::ClubCreate,
            RightEnum::ClubView,
            RightEnum::ClubEdit,
            RightEnum::ClubDestroy,
        ]);
    }

    #region index
    public function test_index(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('club.index'));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
            ->component('club/club-index')
        );
        $this->assertRights(RightEnum::ClubIndex, 'club.index');
    }

    public function test_index_guest_redirect_login(): void
    {
        $response = $this->actingAsGuest()
            ->get(route('club.index'));

        $response->assertRedirect(route('login'));
    }
    #endregion

    #region create
    public function test_create_shows_club_create_view(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('club.create'));

        $response->assertInertia(
            fn ($page) => $page
            ->component('club/club-create')
        );
        $this->assertRights(RightEnum::ClubCreate, 'club.create');
    }

    public function test_create_guest_redirect_login(): void
    {
        $response = $this->get(route('club.create'));

        $response->assertRedirect(route('login'));
    }
    #endregion

    #region store
    public function test_store_creates_club(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('club.store'), [
                'clubname' => 'FC Testhausen',
                'zip_code' => '12345',
                'city' => 'Testhausen',
            ]);

        $response->assertRedirect(route('club.index'));
        $this->assertDatabaseHas('clubs', [
            'clubname' => 'FC Testhausen',
            'zip_code' => '12345',
            'city' => 'Testhausen',
        ]);

        $this->assertRights(RightEnum::ClubCreate, 'club.create');
    }

    public function test_store_guest_redirect_login(): void
    {
        $response = $this->post(route('club.store'), [
            'clubname' => 'FC Testhausen',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_store_succeeds_without_zip_code_and_city(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('club.store'), [
                'clubname' => 'FC Testhausen',
            ]);

        $response->assertValid();
        $response->assertRedirect(route('club.index'));
        $this->assertDatabaseHas('clubs', ['clubname' => 'FC Testhausen', 'zip_code' => null, 'city' => null]);
    }

    public function test_store_fails_without_clubname(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('club.store'), []);

        $response->assertInvalid(['clubname']);
    }

    public function test_store_validates_clubname_max_length(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('club.store'), [
                'clubname' => str_repeat('a', 256),
            ]);

        $response->assertInvalid(['clubname']);
    }

    public function test_store_validates_zip_code_max_length(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('club.store'), [
                'clubname' => 'FC Testhausen',
                'zip_code' => str_repeat('1', 256),
            ]);

        $response->assertInvalid(['zip_code']);
    }

    public function test_store_validates_city_max_length(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('club.store'), [
                'clubname' => 'FC Testhausen',
                'city' => str_repeat('a', 256),
            ]);

        $response->assertInvalid(['city']);
    }
    #endregion

    #region show
    public function test_show_guest_redirect_login(): void
    {
        $club = Club::factory()->create();

        $this->get(route('club.show', $club))
            ->assertRedirect(route('login'));
    }

    public function test_show_renders_modal(): void
    {
        $club = Club::factory()->create([
            'clubname' => 'FC Testhausen',
            'zip_code' => '12345',
            'city' => 'Testhausen',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('club.show', $club));

        $response->assertInertia(
            fn ($page) => $page
            ->component('club/club-index')
            ->where('modal.component', 'club/club-show')
            ->where('modal.props.club.id', $club->id)
            ->where('modal.props.club.clubname', 'FC Testhausen')
            ->where('modal.props.club.zip_code', '12345')
            ->where('modal.props.club.city', 'Testhausen')
        );

        $this->assertRights(RightEnum::ClubView, ['club.show', $club->id]);
    }

    public function test_show_returns_404_for_nonexistent_club(): void
    {
        $this->actingAs($this->user)
            ->get(route('club.show', 999))
            ->assertNotFound();
    }
    #endregion

    #region edit
    public function test_edit_shows_club_edit_view(): void
    {
        $club = Club::factory()->create([
            'clubname' => 'FC Testhausen',
            'zip_code' => '12345',
            'city' => 'Testhausen',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('club.edit', $club));

        $response->assertInertia(
            fn ($page) => $page
            ->component('club/club-edit')
            ->where('club.id', $club->id)
            ->where('club.clubname', 'FC Testhausen')
            ->where('club.zip_code', '12345')
            ->where('club.city', 'Testhausen')
        );

        $this->assertRights(RightEnum::ClubEdit, ['club.edit', $club->id]);
    }

    public function test_edit_guest_redirect_login(): void
    {
        $club = Club::factory()->create();

        $this->get(route('club.edit', $club))
            ->assertRedirect(route('login'));
    }

    public function test_edit_returns_404_for_nonexistent_club(): void
    {
        $this->actingAs($this->user)
            ->get(route('club.edit', 999))
            ->assertNotFound();
    }
    #endregion

    #region update
    public function test_update_changes_club_data(): void
    {
        $club = Club::factory()->create(['clubname' => 'FC Alt']);

        $response = $this->actingAs($this->user)
            ->put(route('club.update', $club), [
                'clubname' => 'FC Neu',
                'zip_code' => '54321',
                'city' => 'Neustadt',
            ]);

        $response->assertRedirect(route('club.index'));
        $this->assertDatabaseHas('clubs', [
            'id' => $club->id,
            'clubname' => 'FC Neu',
            'zip_code' => '54321',
            'city' => 'Neustadt',
        ]);

        $this->assertRights(RightEnum::ClubEdit, ['club.update', $club]);
    }

    public function test_update_guest_redirect_login(): void
    {
        $club = Club::factory()->create();

        $this->put(route('club.update', $club))
            ->assertRedirect(route('login'));
    }

    public function test_update_fails_without_clubname(): void
    {
        $club = Club::factory()->create();

        $response = $this->actingAs($this->user)
            ->put(route('club.update', $club), []);

        $response->assertInvalid(['clubname']);
    }

    public function test_update_validates_clubname_max_length(): void
    {
        $club = Club::factory()->create();

        $response = $this->actingAs($this->user)
            ->put(route('club.update', $club), [
                'clubname' => str_repeat('a', 256),
            ]);

        $response->assertInvalid(['clubname']);
    }

    public function test_update_returns_404_for_nonexistent_club(): void
    {
        $this->actingAs($this->user)
            ->put(route('club.update', 999), [
                'clubname' => 'FC Testhausen',
            ])
            ->assertNotFound();
    }
    #endregion

    #region destroy
    public function test_destroy_deletes_club(): void
    {
        $club = Club::factory()->create();

        $this->assertRights(RightEnum::ClubDestroy, ['club.destroy', $club->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('club.destroy', $club));

        $response->assertRedirect(route('club.index'));
        $this->assertDatabaseMissing('clubs', ['id' => $club->id]);
    }

    public function test_destroy_guest_redirect_login(): void
    {
        $club = Club::factory()->create();

        $response = $this->delete(route('club.destroy', $club));

        $response->assertRedirect(route('login'));
    }

    public function test_destroy_returns_404_for_nonexistent_club(): void
    {
        $this->actingAs($this->user)
            ->delete(route('club.destroy', 999))
            ->assertNotFound();
    }
    #endregion

    #region search
    public function test_search_renders_view_with_clubs(): void
    {
        Club::factory(10)->create();

        $response = $this->actingAs($this->user)
            ->get(route('club.search'));

        $response->assertInertia(
            fn ($page) => $page
            ->component('club/club-search')
            ->has('clubs', 10)
        );
    }

    public function test_search_returns_matching_clubs(): void
    {
        Club::factory()->create(['clubname' => 'FC Testhausen']);
        Club::factory()->create(['clubname' => 'SV Musterstadt']);

        $response = $this->actingAs($this->user)
            ->get(route('club.search', ['clubname' => 'Testhausen']));

        $response->assertInertia(
            fn ($page) => $page
            ->component('club/club-search')
            ->has('clubs', 1)
            ->where('clubs.0.clubname', 'FC Testhausen')
        );
    }

    public function test_search_guest_redirect_login(): void
    {
        $response = $this->get(route('club.search'));

        $response->assertRedirect(route('login'));
    }

    public function test_search_validates_clubname_max_length(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('club.search', ['clubname' => str_repeat('a', 256)]));

        $response->assertInvalid(['clubname']);
    }

    public function test_search_validates_zip_code_max_length(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('club.search', ['zip_code' => str_repeat('1', 256)]));

        $response->assertInvalid(['zip_code']);
    }

    public function test_search_validates_city_max_length(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('club.search', ['city' => str_repeat('a', 256)]));

        $response->assertInvalid(['city']);
    }
    #endregion
}
