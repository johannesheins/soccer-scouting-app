<?php

namespace Feature\Administration;

use PHPUnit\Framework\TestCase;
use Tests\Feature\Administration\AdministrationTest;

class RoleControllerTest extends AdministrationTest
{
    public function test_index()
    {
        $this->assertAdministrationRoute('administration.role.index', 'administration/roles/index');
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
