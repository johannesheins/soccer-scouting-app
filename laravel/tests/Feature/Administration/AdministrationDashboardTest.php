<?php

namespace Tests\Feature\Administration;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdministrationDashboardTest extends AdministrationTest
{
    public function test_administration_dashboard(): void
    {
        $this->assertAdministrationRoute('administration', 'administration/dashboard');
    }
}
