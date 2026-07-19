<?php

namespace Database\Factories;

use App\Models\Evaluation;
use App\Models\EvaluationCriteria;
use App\Models\EvaluationCriteriaScore;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EvaluationCriteriaScoreFactory extends Factory
{
    protected $model = EvaluationCriteriaScore::class;

    public function definition(): array
    {
        return [
            'score' => $this->faker->numberBetween(0, 10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'evaluation_id' => Evaluation::factory(),
            'evaluation_criteria_id' => EvaluationCriteria::factory(),
        ];
    }
}
