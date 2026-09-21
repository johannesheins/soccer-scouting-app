<?php

namespace Tests\Feature\Evaluation;

use App\Models\GameEvaluation;
use App\Models\PlayerEvaluation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationLoadForViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_game_evaluation_loads_shared_and_game_relations(): void
    {
        $evaluation = GameEvaluation::factory()->create()->fresh();

        $result = $evaluation->loadForView();

        $this->assertSame($evaluation, $result);
        foreach (['criteriaScores', 'recommendation', 'creator', 'player', 'homeTeam', 'guestTeam'] as $relation) {
            $this->assertTrue($result->relationLoaded($relation), $relation);
        }
    }

    public function test_player_evaluation_loads_shared_and_player_relations(): void
    {
        $evaluation = PlayerEvaluation::factory()->create()->fresh();

        $result = $evaluation->loadForView();

        $this->assertSame($evaluation, $result);
        foreach (['criteriaScores', 'recommendation', 'creator', 'player'] as $relation) {
            $this->assertTrue($result->relationLoaded($relation), $relation);
        }
    }
}
