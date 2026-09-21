<?php

namespace Tests\Feature\Evaluation;

use App\Enums\EvaluationTypes;
use App\Enums\Permission\EvaluationPermissions;
use App\Enums\Permission\GameEvaluationPermissions;
use App\Enums\Permission\PlayerEvaluationPermissions;
use App\Models\GameEvaluation;
use App\Models\PlayerEvaluation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class EvaluationSearchControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUserWithRight([
            EvaluationPermissions::Search,
            GameEvaluationPermissions::ViewAll,
            PlayerEvaluationPermissions::ViewAll,
        ]);
    }

    public function test_search_renders_evaluations_of_both_types(): void
    {
        $game = GameEvaluation::factory()->create(['created_at' => Carbon::parse('2026-01-01')]);
        $player = PlayerEvaluation::factory()->create(['created_at' => Carbon::parse('2026-02-01')]);

        $response = $this->actingAs($this->user)
            ->get(route('evaluation.search'));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
            ->component('evaluation/evaluation-search')
            ->has('evaluations', 2)
            ->where('evaluations.0.id', $player->id)
            ->where('evaluations.0.evaluation_type', EvaluationTypes::PLAYER->value)
            ->has('evaluations.0.player')
            ->has('evaluations.0.creator')
            ->has('evaluations.0.criteria_scores')
            ->where('evaluations.1.id', $game->id)
            ->where('evaluations.1.evaluation_type', EvaluationTypes::GAME->value)
            ->has('evaluations.1.player')
            ->has('evaluations.1.home_team')
            ->has('evaluations.1.guest_team')
        );
        $this->assertRights(EvaluationPermissions::Search, 'evaluation.search');
    }

    public function test_search_guest_redirect_login(): void
    {
        $response = $this->actingAsGuest()
            ->get(route('evaluation.search'));

        $response->assertRedirect(route('login'));
    }
}
