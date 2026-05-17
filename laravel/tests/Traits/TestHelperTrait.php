<?php

namespace Tests\Traits;

use App\Enums\RightEnum;
use App\Models\Right;
use App\Models\User;
use App\Models\UserGroup;
use Database\Seeders\RightSeeder;

trait TestHelperTrait
{
    public function inRollback(callable $callback): void
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $callback();
        } finally {
            \Illuminate\Support\Facades\DB::rollBack();
        }
    }

    public function route(string|array $route): string
    {
        if (is_array($route)) {
            $routeName = array_first($route);
            $params = array_splice($route, 1);

            return route($routeName, $params);
        }

        return route($route);
    }

    public function method(string $routeName): ?string
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

    /**
     * @param RightEnum[] $rights
     * @return User
     */
    public function createUserWithRight(array $rights): User
    {
        (new RightSeeder())->run();

        $user = User::factory()->create();
        $userGroup = UserGroup::factory()->create();
        foreach ($rights as $right) {
            $rightModel = Right::findOrFail($right);
            $userGroup->rights()->attach($rightModel);
        }
        $userGroup->members()->attach($user);

        return $user;
    }
}
