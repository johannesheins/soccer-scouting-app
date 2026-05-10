<?php

namespace Tests\Feature\Administration;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdministrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $administratorUser;
    protected function setUp(): void{
        parent::setUp();
        $this->administratorUser = User::factory()->administrator()->create();
    }

    public function assertAdministrationRoute(string $routeName, string $component): void{
        $this->administratorCanSeeAdministrationPage($routeName, $component);
        $this->userCanNotSeeAdministrationPage($routeName);
        $this->guestCanNotSeeAdministrationPage($routeName);
    }

    public function administratorCanSeeAdministrationPage(string $routeName, string $component): void
    {
        $user = User::factory()->administrator()->create();

        $response = $this->actingAs($user)
            ->get(route($routeName));

        $response->assertOk();
        $response->assertInertia(fn($page) => $page
            ->component($component));
    }

    public function userCanNotSeeAdministrationPage(string $routeName): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route($routeName));

        $response->assertNotFound();
    }

    public function guestCanNotSeeAdministrationPage(string $routeName): void
    {
        $response = $this->actingAsGuest()
            ->get(route($routeName));

        $response->assertRedirect('login');
    }
}
