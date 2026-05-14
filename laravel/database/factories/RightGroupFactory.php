<?php

namespace Database\Factories;

use App\Models\RightGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class RightGroupFactory extends Factory
{
    protected $model = RightGroup::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
