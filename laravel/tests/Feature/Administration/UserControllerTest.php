<?php

namespace Feature\Administration;

use App\Models\User;
use App\Models\UserGroup;
use Tests\Feature\Administration\AdministrationTest;

class UserControllerTest extends AdministrationTest
{
    public function test_index()
    {
        $users = User::factory(2)->administrator()->create();
        $users[] = User::factory(10)->create();

        $response = $this->actingAs($this->administratorUser)
            ->get(route('administration.user.index'));

        $this->assertAdministrationRoute('administration.user.index', 'administration/user/user-index');
        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('administration/user/user-index')
            ->has('users', 10)
            ->where('users.0.is_administrator', 0)
        );
    }

    public function test_create()
    {
        $userGroups = UserGroup::factory(4)->create();

        $response = $this->actingAs($this->administratorUser)
            ->get(route('administration.user.create'));

        $this->assertAdministrationRoute('administration.user.create', 'administration/user/user-create');
        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('administration/user/user-create')
            ->has('userGroups', 4)
            ->where('userGroups.0.id', $userGroups->first()->id)
        );
    }

    public function test_store()
    {
        $userGroups = UserGroup::factory(4)->create();

        $response = $this->actingAs($this->administratorUser)
            ->post(route('administration.user.store'), [
                'firstname' => 'Test',
                'lastname' => 'User',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'userGroups' => $userGroups->pluck('id')->toArray(),
            ]);

        $this->assertAdministrationRoute('administration.user.store');
        $this->assertDatabaseHas('users', [
            'firstname' => 'Test',
            'lastname' => 'User',
            'email' => 'test@example.com'
        ]);
        $createdUser = User::where('email', 'test@example.com')->first();
        $this->assertDatabaseHas('user_group_members', [
            'user_group_id' => $userGroups->first()->id,
            'user_id' => $createdUser->id,
        ]);
        $response->assertRedirect(route('administration.user.index'));
    }

    public function test_store_empty_request()
    {
        $response = $this->actingAs($this->administratorUser)
            ->post(route('administration.user.store'), []);

        $response->assertInvalid(['firstname', 'lastname', 'email']);
    }

    public function test_store_invalid_request()
    {
        UserGroup::factory(4)->create();

        $response = $this->actingAs($this->administratorUser)
            ->post(route('administration.user.store'), [
                'firstname' => 'Test',
                'lastname' => 'User',
                'email' => 'hfsdlkfsö',
                'password' => 'password',
                'password_confirmation' => 'password123',
                'userGroups' => [10, 11, 12],
            ]);

        $response->assertInvalid(['password', 'email', 'userGroups.0']);
    }

    public function test_show()
    {
        $this->markTestSkipped();
    }

    public function test_edit()
    {
        $userGroups = UserGroup::factory(4)->create();
        $user = User::factory()->create();
        $user->userGroups()->attach($userGroups->take(2)->pluck('id'));

        $response = $this->actingAs($this->administratorUser)
            ->get(route('administration.user.edit', $user->id));

        $this->assertAdministrationRoute(['administration.user.edit', $user->id], 'administration/user/user-edit');
        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('administration/user/user-edit')
            ->has('user')
            ->where('user.id', $user->id)
            ->has('userGroups', 4)
            ->where('userGroups.0.id', $userGroups->first()->id)
        );
    }

    public function test_update()
    {
        $this->markTestSkipped();
    }

    public function test_destroy()
    {
        $this->markTestSkipped();
    }
}
