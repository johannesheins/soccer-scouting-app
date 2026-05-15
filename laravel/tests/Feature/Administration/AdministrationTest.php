<?php

namespace Tests\Feature\Administration;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class AdministrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $administratorUser;
    protected function setUp(): void
    {
        parent::setUp();
        $this->administratorUser = User::factory()->administrator()->create();
    }

    public function assertAdministrationRoute(string|array $route, ?string $component = null): void
    {
        if($component !== null){
            $this->administratorCanSeeAdministrationPage($route, $component);
        }
        $this->userCanNotSeeAdministrationPage($route);
        $this->guestCanNotSeeAdministrationPage($route);
    }

    public function administratorCanSeeAdministrationPage(string|array $route, string $component): void
    {
        $user = User::factory()->administrator()->create();

        $response = $this->actingAs($user)
            ->get($this->route($route));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component($component));
    }

    public function userCanNotSeeAdministrationPage(string|array $routeName): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get($this->route($routeName));

        $response->assertNotFound();
    }

    public function guestCanNotSeeAdministrationPage(string|array $routeName): void
    {
        $response = $this->actingAsGuest()
            ->get($this->route($routeName));

        $response->assertRedirect('login');
    }

    private function route(string|array $route): string
    {
        if(is_array($route)){
            $routeName = array_first($route);
            $params = array_splice($route, 1);

            return route($routeName, $params);
        }

        return route($route);
    }
}
