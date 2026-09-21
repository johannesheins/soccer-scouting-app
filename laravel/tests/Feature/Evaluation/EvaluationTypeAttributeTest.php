<?php

namespace Tests\Feature\Evaluation;

use App\Enums\EvaluationTypes;
use App\Models\GameEvaluation;
use App\Models\PlayerEvaluation;
use Tests\TestCase;

class EvaluationTypeAttributeTest extends TestCase
{
    public function test_game_evaluation_has_game_type(): void
    {
        $this->assertSame(EvaluationTypes::GAME->value, (new GameEvaluation())->evaluation_type);
    }

    public function test_player_evaluation_has_player_type(): void
    {
        $this->assertSame(EvaluationTypes::PLAYER->value, (new PlayerEvaluation())->evaluation_type);
    }

    public function test_game_evaluation_serializes_its_type(): void
    {
        $this->assertSame(EvaluationTypes::GAME->value, (new GameEvaluation())->toArray()['evaluation_type']);
    }

    public function test_player_evaluation_serializes_its_type(): void
    {
        $this->assertSame(EvaluationTypes::PLAYER->value, (new PlayerEvaluation())->toArray()['evaluation_type']);
    }
}
