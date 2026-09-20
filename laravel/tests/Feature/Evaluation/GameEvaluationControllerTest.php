<?php

namespace Tests\Feature\Evaluation;

use App\Enums\Permission\GameEvaluationPermissions;
use App\Models\Club;
use App\Models\GameEvaluation;
use App\Models\Player;

class GameEvaluationControllerTest extends EvaluationControllerTest
{
    protected function modelClass(): string
    {
        return GameEvaluation::class;
    }

    protected function routeName(): string
    {
        return 'game-evaluation';
    }

    protected function permissionEnum(): string
    {
        return GameEvaluationPermissions::class;
    }

    protected function componentPrefix(): string
    {
        return 'evaluation/game/game-evaluation';
    }

    protected function typePayload(): array
    {
        return [
            'player_id' => Player::factory()->create()->id,
            'home_team_id' => Club::factory()->create()->id,
            'guest_team_id' => Club::factory()->create()->id,
        ];
    }

    protected function requiredTypeFields(): array
    {
        return ['player_id', 'home_team_id', 'guest_team_id'];
    }

    public function test_store_validates_player_exists(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route($this->typeRoute('store')), $this->payload(['player_id' => 999]));

        $response->assertInvalid(['player_id']);
    }

    public function test_store_validates_home_team_exists(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route($this->typeRoute('store')), $this->payload(['home_team_id' => 999]));

        $response->assertInvalid(['home_team_id']);
    }

    public function test_store_validates_guest_team_exists(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route($this->typeRoute('store')), $this->payload(['guest_team_id' => 999]));

        $response->assertInvalid(['guest_team_id']);
    }
}
