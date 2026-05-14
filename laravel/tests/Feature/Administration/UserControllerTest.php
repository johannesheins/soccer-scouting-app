<?php

namespace Feature\Administration;

use App\Models\User;
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

    }

    public function test_store()
    {

    }

    public function test_show()
    {

    }

    public function test_edit()
    {

    }

    public function test_update()
    {

    }

    public function test_destroy()
    {

    }
}
