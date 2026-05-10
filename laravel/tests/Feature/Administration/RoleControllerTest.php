<?php

namespace Feature\Administration;

use App\Models\Role;
use App\Models\User;
use PHPUnit\Framework\TestCase;
use Tests\Feature\Administration\AdministrationTest;

class RoleControllerTest extends AdministrationTest
{
    public function test_index()
    {
        $roles = Role::factory(14)->create();

        $response = $this->actingAs($this->administratorUser)
            ->get(route('administration.role.index'));

        $this->assertAdministrationRoute('administration.role.index', 'administration/role/role-index');
        $response->assertOk();
        $response->assertInertia(fn($page) => $page
            ->component('administration/role/role-index')
            ->has('roles', 14)
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
