<?php

namespace Tests;

use App\Enums\RightEnum;
use App\Models\Right;
use App\Models\User;
use App\Models\UserGroup;
use Database\Seeders\RightSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Fortify\Features;

abstract class TestCase extends BaseTestCase
{
    protected function skipUnlessFortifyHas(string $feature, ?string $message = null): void
    {
        if (! Features::enabled($feature)) {
            $this->markTestSkipped($message ?? "Fortify feature [{$feature}] is not enabled.");
        }
    }

    public function assertRights(RightEnum $right, string|array $route, ?string $method = null): void
    {
        $rightSeeder = new RightSeeder();
        $rightSeeder->run();

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
            $user = User::factory()->create();
            $userGroup = UserGroup::factory()->create();
            $rightModel = Right::where('enum_case', $right->value)->firstOrFail();
            $userGroup->rights()->attach($rightModel);
            $userGroup->members()->attach($user);

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

    private function inRollback(callable $callback): void
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $callback();
        } finally {
            \Illuminate\Support\Facades\DB::rollBack();
        }
    }

    protected function route(string|array $route): string
    {
        if (is_array($route)) {
            $routeName = array_first($route);
            $params = array_splice($route, 1);

            return route($routeName, $params);
        }

        return route($route);
    }

    protected function method(string $routeName): ?string
    {
        return match (true) {
            str_ends_with($routeName, '.index'),
            str_ends_with($routeName, '.show'),
            str_ends_with($routeName, '.create'),
            str_ends_with($routeName, '.edit'),
            str_ends_with($routeName, '.search') => 'get',

            str_ends_with($routeName, '.store') => 'post',

            str_ends_with($routeName, '.update') => 'put',

            str_ends_with($routeName, '.destroy') => 'delete',

            default => null
        };
    }
}
