<?php

namespace Tests\Feature\Administration;

class AdministrationDashboardTest extends AdministrationTest
{
    public function test_administration_dashboard(): void
    {
        $this->assertAdministrationRoute('administration', 'administration/dashboard');
    }
}
