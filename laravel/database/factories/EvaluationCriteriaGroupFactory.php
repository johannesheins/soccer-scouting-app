<?php

namespace Database\Factories;

use App\Models\EvaluationCriteriaGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvaluationCriteriaGroupFactory extends Factory
{
    protected $model = EvaluationCriteriaGroup::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
        ];
    }
}