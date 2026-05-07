<?php

namespace Database\Factories;

use App\Models\PositionGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class PositionGroupFactory extends Factory
{
    protected $model = PositionGroup::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
