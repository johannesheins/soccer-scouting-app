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
        return 'evaluation.game';
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
            'home_club_id' => Club::factory()->create()->id,
            'guest_club_id' => Club::factory()->create()->id,
        ];
    }

    protected function requiredTypeFields(): array
    {
        return ['player_id', 'home_club_id', 'guest_club_id'];
    }

    public function test_show_includes_home_and_guest_club(): void
    {
        $evaluation = GameEvaluation::factory()->create(['created_by' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route($this->typeRoute('show'), $evaluation));

        $response->assertInertia(
            fn ($page) => $page
            ->where('modal.props.evaluation.home_club.id', $evaluation->home_club_id)
            ->where('modal.props.evaluation.guest_club.id', $evaluation->guest_club_id)
        );
    }

    public function test_store_validates_player_exists(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route($this->typeRoute('store')), $this->payload(['player_id' => 999]));

        $response->assertInvalid(['player_id']);
    }

    public function test_store_validates_home_club_exists(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route($this->typeRoute('store')), $this->payload(['home_club_id' => 999]));

        $response->assertInvalid(['home_club_id']);
    }

    public function test_store_validates_guest_club_exists(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route($this->typeRoute('store')), $this->payload(['guest_club_id' => 999]));

        $response->assertInvalid(['guest_club_id']);
    }
}
