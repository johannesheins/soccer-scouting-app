<?php

namespace Tests\Feature\Evaluation;

use App\Models\GameEvaluation;
use App\Models\PlayerEvaluation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationCreatorSoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_game_evaluation_keeps_soft_deleted_creator(): void
    {
        $evaluation = GameEvaluation::factory()->create();
        $evaluation->creator->delete();

        $result = $evaluation->fresh()->loadForView();

        $this->assertNotNull($result->creator);
        $this->assertTrue($result->creator->trashed());
    }

    public function test_player_evaluation_keeps_soft_deleted_creator(): void
    {
        $evaluation = PlayerEvaluation::factory()->create();
        $evaluation->creator->delete();

        $result = $evaluation->fresh()->loadForView();

        $this->assertNotNull($result->creator);
        $this->assertTrue($result->creator->trashed());
    }
}
