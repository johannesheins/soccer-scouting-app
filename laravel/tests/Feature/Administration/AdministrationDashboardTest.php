<?php

namespace Tests\Feature\Administration;

class AdministrationDashboardTest extends AdministrationTestCase
{
    public function test_administration_dashboard(): void
    {
        $this->assertAdministrationRoute('administration', 'administration/dashboard');
    }
}
