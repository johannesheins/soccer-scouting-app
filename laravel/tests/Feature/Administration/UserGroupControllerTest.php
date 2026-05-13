<?php

namespace Feature\Administration;

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
        $response = $this->actingAs($this->administratorUser)
            ->get(route('administration.user-group.create'));

        $this->assertAdministrationRoute('administration.user-group.create', 'administration/user-group/create');
        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
            ->component('administration/user-group/create')
            ->has('rights', 14)
        );
    }

    public function test_store()
    {
        $this->assertTrue(true);
    }

    public function test_show()
    {
        $this->assertTrue(true);
    }

    public function test_update()
    {
        $this->assertTrue(true);
    }

    public function test_destroy()
    {
        $this->assertTrue(true);
    }
}
