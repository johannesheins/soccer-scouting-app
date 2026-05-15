<?php

namespace Database\Factories;

use App\Models\Right;
use App\Models\RightGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class RightFactory extends Factory
{
    protected $model = Right::class;

    public function definition(): array
    {
        return [
            'right_group_id' => RightGroup::factory(),
            'enum_case' => $this->faker->unique()->word(),
            'name' => $this->faker->unique()->name(),
            'description' => $this->faker->text(),
        ];
    }
}
