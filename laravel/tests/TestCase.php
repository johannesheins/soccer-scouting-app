<?php

namespace Tests;

use App\Enums\RightEnum;
use App\Models\Right;
use App\Models\User;
use App\Models\UserGroup;
use Database\Seeders\RightSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Fortify\Features;
use Tests\Traits\TestHelperTrait;

abstract class TestCase extends BaseTestCase
{
    use TestHelperTrait;

    protected function skipUnlessFortifyHas(string $feature, ?string $message = null): void
    {
        if (! Features::enabled($feature)) {
            $this->markTestSkipped($message ?? "Fortify feature [{$feature}] is not enabled.");
        }
    }

    public function assertRights(RightEnum $right, string|array $route, ?string $method = null): void
    {
        $this->assertHasRight($right, $route, $method);
        $this->assertHasRightAsAdministrator($right, $route, $method);
        $this->assertHasNoRight($right, $route, $method);
    }

    private function assertHasNoRight(RightEnum $right, string|array $route, ?string $method = null): void
    {
        $this->inRollback(function () use ($right, $route, $method): void {
            $user = User::factory()->create();
            $routeName = is_array($route) ? $route[0] : $route;
            $method ??= $this->method($routeName) ?? 'get';

            $response = $this->actingAs($user)
                ->$method($this->route($route));

            $response->assertForbidden();
        });
    }

    private function assertHasRight(RightEnum $right, string|array $route, ?string $method = null): void
    {
        $this->inRollback(function () use ($right, $route, $method): void {
            $user = $this->createUserWithRight([$right]);

            $routeName = is_array($route) ? $route[0] : $route;
            $method ??= $this->method($routeName) ?? 'get';

            $response = $this->actingAs($user)
                ->$method($this->route($route));

            self::assertNotSame(403, $response->getStatusCode(), "User with right [{$right->value}] should not receive 403.");
        });
    }

    private function assertHasRightAsAdministrator(RightEnum $right, string|array $route, ?string $method = null): void
    {
        $this->inRollback(function () use ($right, $route, $method): void {
            $user = User::factory()->administrator()->create();

            $routeName = is_array($route) ? $route[0] : $route;
            $method ??= $this->method($routeName) ?? 'get';

            $response = $this->actingAs($user)
                ->$method($this->route($route));

            self::assertNotSame(403, $response->getStatusCode(), "Administrator should not receive 403 for right [{$right->value}].");
        });
    }
}
