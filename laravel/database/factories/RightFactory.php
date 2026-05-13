<?php

namespace Database\Factories;

use App\Models\Right;
use Illuminate\Database\Eloquent\Factories\Factory;

class RightFactory extends Factory
{
    protected $model = Right::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->name(),
            'description' => $this->faker->text(),
        ];
    }
}
