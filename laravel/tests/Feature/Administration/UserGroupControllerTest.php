<?php

namespace Feature\Administration;

use App\Models\Right;
use App\Models\RightGroup;
use App\Models\UserGroup;
use Tests\Feature\Administration\AdministrationTest;

class UserGroupControllerTest extends AdministrationTest
{
    public function test_index()
    {
        $userGroups = UserGroup::factory(14)->create();

        $response = $this->actingAs($this->administratorUser)
            ->get(route('administration.user-group.index'));

        $this->assertAdministrationRoute('administration.user-group.index', 'administration/user-group/user-group-index');
        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
            ->component('administration/user-group/user-group-index')
            ->has('userGroups', 14)
        );
    }

    public function test_create(){
        $rightGroups = RightGroup::factory(3)->create();
        $rightGroups->each(fn($rightGroup) => Right::factory(3)->for($rightGroup)->create());

        $response = $this->actingAs($this->administratorUser)
            ->get(route('administration.user-group.create'));

        $this->assertAdministrationRoute('administration.user-group.create', 'administration/user-group/user-group-create');
        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
            ->component('administration/user-group/user-group-create')
            ->has('rightGroups', 3)
            ->has('rightGroups.0.rights', 3)
            ->has('rightGroups.1.rights', 3)
            ->has('rightGroups.2.rights', 3)
        );
    }

    public function test_store()
    {
        $userGroup = UserGroup::factory()->make();
        $rights = Right::factory(12)->create();

        $response = $this->actingAs($this->administratorUser)
            ->post(route('administration.user-group.store'), [
                'name' => $userGroup->name,
                'rights' => $rights->pluck('id')->toArray(),
            ]);

        $this->assertAdministrationRoute('administration.user-group.create', 'administration/user-group/user-group-create');
        $this->assertDatabaseHas('user_groups', ['name' => $userGroup->name]);
        $this->assertDatabaseHas('user_group_rights', ['right_id' => $rights->first()->id]);

        $response->assertRedirect(route('administration.user-group.index'));
    }

    public function test_store_empty_request()
    {
        $response = $this->actingAs($this->administratorUser)
            ->post(route('administration.user-group.store'), [
                'name' => '',
                'rights' => [],
            ]);

        $response->assertInvalid(['name']);
    }

    public function test_store_invalid_request()
    {
        $response = $this->actingAs($this->administratorUser)
            ->post(route('administration.user-group.store'), [
                'name' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab aliquam architecto atque, aut commodi, cumque distinctio doloribus eum explicabo fugit illo inventore ipsam minima natus nemo nesciunt nulla porro possimus quasi quo quod recusandae repellendus reprehenderit sunt totam velit veritatis vitae. Ad adipisci assumenda at, corporis culpa cumque enim eos iure neque perspiciatis quae, quo reiciendis rem repellendus repudiandae tempore vel voluptate, voluptatibus! Aperiam autem blanditiis cum delectus dolores enim excepturi facere hic, id labore maxime natus nisi nostrum quae quaerat quis quod suscipit vel, velit vitae! Ad architecto asperiores at culpa eos ex illo magnam, nesciunt nihil omnis soluta.',
                'rights' => [1, 3, 4]
            ]);

        $response->assertInvalid(['name', 'rights.0']);
    }

    public function test_show()
    {
        $userGroup = UserGroup::factory()->create();

        $response = $this->actingAs($this->administratorUser)
            ->get(route('administration.user-group.show', $userGroup->id));

        $this->assertAdministrationRoute(['administration.user-group.show', $userGroup->id]);
        $response->assertNotFound();
    }

    public function test_edit(){
        $rightGroups = RightGroup::factory(3)->create();
        $rightGroups->each(fn($rightGroup) => Right::factory(3)->for($rightGroup)->create());

        $userGroup = UserGroup::factory()->create();
        $userGroup->rights()->attach($rightGroups->flatMap->rights->take(10));

        $response = $this->actingAs($this->administratorUser)
            ->get(route('administration.user-group.edit', $userGroup->id));

        $this->assertAdministrationRoute(['administration.user-group.edit', $userGroup->id], 'administration/user-group/user-group-edit');
        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
            ->component('administration/user-group/user-group-edit')

            ->has('rightGroups', 3)
            ->has('rightGroups.0.rights', 3)

            ->has('userGroup')
            ->has('userGroup.rights', 9)
            ->where('userGroup.rights.0.id', $userGroup->rights()->first()->id)
        );
    }

    public function test_update()
    {
        $oldRights = Right::factory(2)->create();
        $userGroup = UserGroup::factory()->create();
        $userGroup->rights()->attach($oldRights);

        $newName = 'Updated Group';
        $newRights = Right::factory(3)->create();

        $response = $this->actingAs($this->administratorUser)
            ->put(route('administration.user-group.update', $userGroup->id), [
                'name' => $newName,
                'rights' => $newRights->pluck('id')->toArray(),
            ]);

        $this->assertAdministrationRoute(['administration.user-group.edit', $userGroup->id], 'administration/user-group/user-group-edit');
        $this->assertDatabaseHas('user_groups', ['id' => $userGroup->id, 'name' => $newName]);
        $this->assertDatabaseHas('user_group_rights', ['user_group_id' => $userGroup->id, 'right_id' => $newRights->first()->id]);
        $this->assertDatabaseMissing('user_group_rights', ['user_group_id' => $userGroup->id, 'right_id' => $oldRights->first()->id]);

        $response->assertRedirect(route('administration.user-group.index'));
    }

    public function test_update_empty_request()
    {
        $userGroup = UserGroup::factory()->create();

        $response = $this->actingAs($this->administratorUser)
            ->put(route('administration.user-group.update', $userGroup->id), [
                'name' => '',
                'rights' => [],
            ]);

        $response->assertInvalid(['name']);
    }

    public function test_update_invalid_request()
    {
        $userGroup = UserGroup::factory()->create();

        $response = $this->actingAs($this->administratorUser)
            ->put(route('administration.user-group.update', $userGroup->id), [
                'name' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab aliquam architecto atque, aut commodi, cumque distinctio doloribus eum explicabo fugit illo inventore ipsam minima natus nemo nesciunt nulla porro possimus quasi quo quod recusandae repellendus reprehenderit sunt totam velit veritatis vitae. Ad adipisci assumenda at, corporis culpa cumque enim eos iure neque perspiciatis quae, quo reiciendis rem repellendus repudiandae tempore vel voluptate, voluptatibus! Aperiam autem blanditiis cum delectus dolores enim excepturi facere hic, id labore maxime natus nisi nostrum quae quaerat quis quod suscipit vel, velit vitae! Ad architecto asperiores at culpa eos ex illo magnam, nesciunt nihil omnis soluta.',
                'rights' => [1, 3, 4]
            ]);

        $response->assertInvalid(['name', 'rights.0']);
    }

    public function test_destroy()
    {
        $userGroup = UserGroup::factory()->create();
        $userGroup->rights()->attach(Right::factory(3)->create());

        $response = $this->actingAs($this->administratorUser)
            ->delete(route('administration.user-group.destroy', $userGroup->id));

        $this->assertAdministrationRoute(['administration.user-group.destroy', $userGroup->id]);
        $this->assertDatabaseMissing('user_groups', ['id' => $userGroup->id]);
        $this->assertDatabaseMissing('user_group_rights', ['user_group_id' => $userGroup->id]);

        $response->assertRedirect(route('administration.user-group.index'));

    }
}
