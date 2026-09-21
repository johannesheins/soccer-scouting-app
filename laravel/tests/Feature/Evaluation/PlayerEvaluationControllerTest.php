<?php

namespace Tests\Feature\Evaluation;

use App\Enums\Permission\PlayerEvaluationPermissions;
use App\Models\Player;
use App\Models\PlayerEvaluation;

class PlayerEvaluationControllerTest extends EvaluationControllerTest
{
    protected function modelClass(): string
    {
        return PlayerEvaluation::class;
    }

    protected function routeName(): string
    {
        return 'evaluation.player';
    }

    protected function permissionEnum(): string
    {
        return PlayerEvaluationPermissions::class;
    }

    protected function componentPrefix(): string
    {
        return 'evaluation/player/player-evaluation';
    }

    protected function typePayload(): array
    {
        return [
            'player_id' => Player::factory()->create()->id,
        ];
    }

    protected function requiredTypeFields(): array
    {
        return ['player_id'];
    }

    public function test_store_validates_player_exists(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route($this->typeRoute('store')), $this->payload(['player_id' => 999]));

        $response->assertInvalid(['player_id']);
    }
}
