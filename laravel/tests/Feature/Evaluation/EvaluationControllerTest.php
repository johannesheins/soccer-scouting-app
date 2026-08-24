<?php

namespace Tests\Feature\Evaluation;

use App\Enums\RightEnum;
use App\Models\Club;
use App\Models\Evaluation;
use App\Models\EvaluationCriteria;
use App\Models\EvaluationCriteriaGroup;
use App\Models\EvaluationCriteriaScore;
use App\Models\Player;
use App\Models\Position;
use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUserWithRight([
            RightEnum::EvaluationIndex,
            RightEnum::EvaluationSearch,
            RightEnum::EvaluationCreate,
            RightEnum::EvaluationView,
            RightEnum::EvaluationViewAll,
            RightEnum::EvaluationEdit,
            RightEnum::EvaluationEditAll,
            RightEnum::EvaluationDestroy,
            RightEnum::EvaluationDestroyAll
        ]);
    }
    public function test_index()
    {
        $response = $this->actingAs($this->user)
            ->get(route('evaluation.index'));

        $response->assertOk();
        $this->assertRights(RightEnum::EvaluationIndex, 'evaluation.index');
    }

    public function test_index_guest_redirect_login(): void
    {
        $response = $this->actingAsGuest()
            ->get(route('evaluation.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_create()
    {
        Club::factory(10)->create();
        Position::factory(8)->create();

        foreach (EvaluationCriteriaGroup::factory(3)->create() as $criteriaGroup) {
            EvaluationCriteria::factory(3)->create(['evaluation_criteria_group_id' => $criteriaGroup->id]);
        }

        Recommendation::factory(4)->create();

        $response = $this->actingAs($this->user)
            ->get(route('evaluation.create'));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
            ->component('evaluation/evaluation-create')
            ->has('clubs', 10)
            ->has('positions', 8)
            ->has('evaluationCriteriaGroups', 3)
            ->has('evaluationCriteriaGroups.0.evaluation_criteria', 3)
            ->has('recommendations', 4)
        );
        $this->assertRights(RightEnum::EvaluationCreate, 'evaluation.create');
    }

    public function test_store_creates_evaluation(): void
    {
        $player = Player::factory()->create();
        $homeTeam = Club::factory()->create();
        $awayTeam = Club::factory()->create();
        $recommendation = Recommendation::factory()->create();
        $criteria = EvaluationCriteria::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('evaluation.store'), [
                'player_id' => $player->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'kickoff_date' => '2026-08-01',
                'kickoff_time' => '15:30',
                'strengths' => 'Good positioning',
                'weaknesses' => 'Weak on the left foot',
                'recommendation_id' => $recommendation->id,
                'comment' => 'Promising talent',
                'criteriaScores' => [
                    ['evaluation_criteria_id' => $criteria->id, 'score' => 8],
                ],
            ]);

        $response->assertRedirect(route('evaluation.index'));
        $this->assertDatabaseHas('evaluations', [
            'player_id' => $player->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'strengths' => 'Good positioning',
            'weaknesses' => 'Weak on the left foot',
            'recommendation_id' => $recommendation->id,
            'comment' => 'Promising talent',
            'created_by' => $this->user->id,
        ]);

        $evaluation = Evaluation::where('player_id', $player->id)->firstOrFail();
        $this->assertSame('2026-08-01', $evaluation->kickoff_date->toDateString());
        $this->assertSame('15:30', $evaluation->kickoff_time->format('H:i'));

        $this->assertDatabaseHas('evaluation_criteria_scores', [
            'evaluation_id' => $evaluation->id,
            'evaluation_criteria_id' => $criteria->id,
            'score' => 8,
        ]);

        $this->assertRights(RightEnum::EvaluationCreate, 'evaluation.store');
    }

    public function test_store_creates_a_criteria_score_for_each_entry(): void
    {
        $player = Player::factory()->create();
        $homeTeam = Club::factory()->create();
        $awayTeam = Club::factory()->create();
        $criteria = EvaluationCriteria::factory(3)->create();

        $response = $this->actingAs($this->user)
            ->post(route('evaluation.store'), [
                'player_id' => $player->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'kickoff_date' => '2026-08-01',
                'kickoff_time' => '15:30',
                'criteriaScores' => $criteria->map(fn ($criterion, $index) => [
                    'evaluation_criteria_id' => $criterion->id,
                    'score' => $index + 1,
                ])->all(),
            ]);

        $response->assertRedirect(route('evaluation.index'));
        $evaluation = Evaluation::where('player_id', $player->id)->firstOrFail();

        $this->assertSame(3, $evaluation->criteriaScores()->count());
        $criteria->each(fn ($criterion, $index) => $this->assertDatabaseHas('evaluation_criteria_scores', [
            'evaluation_id' => $evaluation->id,
            'evaluation_criteria_id' => $criterion->id,
            'score' => $index + 1,
        ]));
    }

    public function test_store_guest_redirect_login(): void
    {
        $player = Player::factory()->create();
        $homeTeam = Club::factory()->create();
        $awayTeam = Club::factory()->create();
        $criteria = EvaluationCriteria::factory()->create();

        $response = $this->post(route('evaluation.store'), [
            'player_id' => $player->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'kickoff_date' => '2026-08-01',
            'kickoff_time' => '15:30',
            'criteriaScores' => [
                ['evaluation_criteria_id' => $criteria->id, 'score' => 8],
            ],
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('evaluation.store'), []);

        $response->assertInvalid([
            'player_id',
            'home_team_id',
            'away_team_id',
            'kickoff_date',
            'kickoff_time',
            'criteriaScores',
        ]);
    }

    public function test_store_validates_player_exists(): void
    {
        $homeTeam = Club::factory()->create();
        $awayTeam = Club::factory()->create();
        $criteria = EvaluationCriteria::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('evaluation.store'), [
                'player_id' => 999,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'kickoff_date' => '2026-08-01',
                'kickoff_time' => '15:30',
                'criteriaScores' => [
                    ['evaluation_criteria_id' => $criteria->id, 'score' => 8],
                ],
            ]);

        $response->assertInvalid(['player_id']);
    }

    public function test_store_validates_home_team_exists(): void
    {
        $player = Player::factory()->create();
        $awayTeam = Club::factory()->create();
        $criteria = EvaluationCriteria::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('evaluation.store'), [
                'player_id' => $player->id,
                'home_team_id' => 999,
                'away_team_id' => $awayTeam->id,
                'kickoff_date' => '2026-08-01',
                'kickoff_time' => '15:30',
                'criteriaScores' => [
                    ['evaluation_criteria_id' => $criteria->id, 'score' => 8],
                ],
            ]);

        $response->assertInvalid(['home_team_id']);
    }

    public function test_store_validates_away_team_exists(): void
    {
        $player = Player::factory()->create();
        $homeTeam = Club::factory()->create();
        $criteria = EvaluationCriteria::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('evaluation.store'), [
                'player_id' => $player->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => 999,
                'kickoff_date' => '2026-08-01',
                'kickoff_time' => '15:30',
                'criteriaScores' => [
                    ['evaluation_criteria_id' => $criteria->id, 'score' => 8],
                ],
            ]);

        $response->assertInvalid(['away_team_id']);
    }

    public function test_store_validates_kickoff_date_is_date(): void
    {
        $player = Player::factory()->create();
        $homeTeam = Club::factory()->create();
        $awayTeam = Club::factory()->create();
        $criteria = EvaluationCriteria::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('evaluation.store'), [
                'player_id' => $player->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'kickoff_date' => 'not-a-date',
                'kickoff_time' => '15:30',
                'criteriaScores' => [
                    ['evaluation_criteria_id' => $criteria->id, 'score' => 8],
                ],
            ]);

        $response->assertInvalid(['kickoff_date']);
    }

    public function test_store_validates_kickoff_time_format(): void
    {
        $player = Player::factory()->create();
        $homeTeam = Club::factory()->create();
        $awayTeam = Club::factory()->create();
        $criteria = EvaluationCriteria::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('evaluation.store'), [
                'player_id' => $player->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'kickoff_date' => '2026-08-01',
                'kickoff_time' => 'not-a-time',
                'criteriaScores' => [
                    ['evaluation_criteria_id' => $criteria->id, 'score' => 8],
                ],
            ]);

        $response->assertInvalid(['kickoff_time']);
    }

    public function test_store_validates_recommendation_exists(): void
    {
        $player = Player::factory()->create();
        $homeTeam = Club::factory()->create();
        $awayTeam = Club::factory()->create();
        $criteria = EvaluationCriteria::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('evaluation.store'), [
                'player_id' => $player->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'kickoff_date' => '2026-08-01',
                'kickoff_time' => '15:30',
                'recommendation_id' => 999,
                'criteriaScores' => [
                    ['evaluation_criteria_id' => $criteria->id, 'score' => 8],
                ],
            ]);

        $response->assertInvalid(['recommendation_id']);
    }

    public function test_store_validates_strengths_max_length(): void
    {
        $player = Player::factory()->create();
        $homeTeam = Club::factory()->create();
        $awayTeam = Club::factory()->create();
        $criteria = EvaluationCriteria::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('evaluation.store'), [
                'player_id' => $player->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'kickoff_date' => '2026-08-01',
                'kickoff_time' => '15:30',
                'strengths' => str_repeat('a', 256),
                'criteriaScores' => [
                    ['evaluation_criteria_id' => $criteria->id, 'score' => 8],
                ],
            ]);

        $response->assertInvalid(['strengths']);
    }

    public function test_store_validates_weaknesses_max_length(): void
    {
        $player = Player::factory()->create();
        $homeTeam = Club::factory()->create();
        $awayTeam = Club::factory()->create();
        $criteria = EvaluationCriteria::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('evaluation.store'), [
                'player_id' => $player->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'kickoff_date' => '2026-08-01',
                'kickoff_time' => '15:30',
                'weaknesses' => str_repeat('a', 256),
                'criteriaScores' => [
                    ['evaluation_criteria_id' => $criteria->id, 'score' => 8],
                ],
            ]);

        $response->assertInvalid(['weaknesses']);
    }

    public function test_store_validates_comment_max_length(): void
    {
        $player = Player::factory()->create();
        $homeTeam = Club::factory()->create();
        $awayTeam = Club::factory()->create();
        $criteria = EvaluationCriteria::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('evaluation.store'), [
                'player_id' => $player->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'kickoff_date' => '2026-08-01',
                'kickoff_time' => '15:30',
                'comment' => str_repeat('a', 65536),
                'criteriaScores' => [
                    ['evaluation_criteria_id' => $criteria->id, 'score' => 8],
                ],
            ]);

        $response->assertInvalid(['comment']);
    }

    public function test_store_validates_criteria_scores_evaluation_criteria_id_exists(): void
    {
        $player = Player::factory()->create();
        $homeTeam = Club::factory()->create();
        $awayTeam = Club::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('evaluation.store'), [
                'player_id' => $player->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'kickoff_date' => '2026-08-01',
                'kickoff_time' => '15:30',
                'criteriaScores' => [
                    ['evaluation_criteria_id' => 999, 'score' => 8],
                ],
            ]);

        $response->assertInvalid(['criteriaScores.0.evaluation_criteria_id']);
    }

    public function test_store_validates_criteria_scores_score_is_numeric(): void
    {
        $player = Player::factory()->create();
        $homeTeam = Club::factory()->create();
        $awayTeam = Club::factory()->create();
        $criteria = EvaluationCriteria::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('evaluation.store'), [
                'player_id' => $player->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'kickoff_date' => '2026-08-01',
                'kickoff_time' => '15:30',
                'criteriaScores' => [
                    ['evaluation_criteria_id' => $criteria->id, 'score' => 'not-a-number'],
                ],
            ]);

        $response->assertInvalid(['criteriaScores.0.score']);
    }

    public function test_store_validates_criteria_scores_score_within_range(): void
    {
        $player = Player::factory()->create();
        $homeTeam = Club::factory()->create();
        $awayTeam = Club::factory()->create();
        $criteria = EvaluationCriteria::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('evaluation.store'), [
                'player_id' => $player->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'kickoff_date' => '2026-08-01',
                'kickoff_time' => '15:30',
                'criteriaScores' => [
                    ['evaluation_criteria_id' => $criteria->id, 'score' => 11],
                ],
            ]);

        $response->assertInvalid(['criteriaScores.0.score']);
    }

    public function test_edit_show_evaluation_update_view()
    {
        $clubs = Club::factory(10)->create();
        Position::factory(8)->create();

        foreach (EvaluationCriteriaGroup::factory(3)->create() as $criteriaGroup) {
            EvaluationCriteria::factory(3)->create(['evaluation_criteria_group_id' => $criteriaGroup->id]);
        }

        $recommendations = Recommendation::factory(4)->create();
        $player = Player::factory()->create(['club_id' => $clubs->first()->id]);
        $evaluation = Evaluation::factory()->create([
            'created_by' => $this->user->id,
            'player_id' => $player->id,
            'home_team_id' => $clubs->first()->id,
            'away_team_id' => $clubs->last()->id,
            'recommendation_id' => $recommendations->first()->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('evaluation.edit', $evaluation));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
            ->component('evaluation/evaluation-edit')
            ->where('evaluation.id', $evaluation->id)
            ->has('clubs', 10)
            ->has('positions', 8)
            ->has('evaluationCriteriaGroups', 3)
            ->has('evaluationCriteriaGroups.0.evaluation_criteria', 3)
            ->has('recommendations', 4)
        );
        $this->assertRights(RightEnum::EvaluationEditAll, ['evaluation.edit', $evaluation]);
    }

    public function test_edit_guest_redirect_login(): void
    {
        $evaluation = Evaluation::factory()->create();

        $response = $this->get(route('evaluation.edit', $evaluation));

        $response->assertRedirect(route('login'));
    }

    public function test_update_updates_evaluation(): void
    {
        $evaluation = Evaluation::factory()->create(['created_by' => $this->user->id]);
        $player = Player::factory()->create();
        $homeTeam = Club::factory()->create();
        $awayTeam = Club::factory()->create();
        $recommendation = Recommendation::factory()->create();
        $criteria = EvaluationCriteria::factory()->create();

        $response = $this->actingAs($this->user)
            ->put(route('evaluation.update', $evaluation), [
                'player_id' => $player->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'kickoff_date' => '2026-08-01',
                'kickoff_time' => '15:30',
                'strengths' => 'Good positioning',
                'weaknesses' => 'Weak on the left foot',
                'recommendation_id' => $recommendation->id,
                'comment' => 'Promising talent',
                'criteriaScores' => [
                    ['evaluation_criteria_id' => $criteria->id, 'score' => 8],
                ],
            ]);

        $response->assertRedirect(route('evaluation.index'));
        $this->assertDatabaseHas('evaluations', [
            'id' => $evaluation->id,
            'player_id' => $player->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'strengths' => 'Good positioning',
            'weaknesses' => 'Weak on the left foot',
            'recommendation_id' => $recommendation->id,
            'comment' => 'Promising talent',
        ]);

        $evaluation->refresh();
        $this->assertSame('2026-08-01', $evaluation->kickoff_date->toDateString());
        $this->assertSame('15:30', $evaluation->kickoff_time->format('H:i'));

        $this->assertDatabaseHas('evaluation_criteria_scores', [
            'evaluation_id' => $evaluation->id,
            'evaluation_criteria_id' => $criteria->id,
            'score' => 8,
        ]);

        $this->assertRights(RightEnum::EvaluationEditAll, ['evaluation.update', $evaluation]);
    }

    public function test_update_replaces_criteria_scores(): void
    {
        $evaluation = Evaluation::factory()->create(['created_by' => $this->user->id]);
        $player = Player::factory()->create();
        $homeTeam = Club::factory()->create();
        $awayTeam = Club::factory()->create();
        $oldCriteria = EvaluationCriteria::factory()->create();
        $newCriteria = EvaluationCriteria::factory()->create();

        EvaluationCriteriaScore::factory()->create([
            'evaluation_id' => $evaluation->id,
            'evaluation_criteria_id' => $oldCriteria->id,
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('evaluation.update', $evaluation), [
                'player_id' => $player->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'kickoff_date' => '2026-08-01',
                'kickoff_time' => '15:30',
                'criteriaScores' => [
                    ['evaluation_criteria_id' => $newCriteria->id, 'score' => 5],
                ],
            ]);

        $response->assertRedirect(route('evaluation.index'));
        $this->assertSame(1, $evaluation->criteriaScores()->count());
        $this->assertDatabaseMissing('evaluation_criteria_scores', [
            'evaluation_id' => $evaluation->id,
            'evaluation_criteria_id' => $oldCriteria->id,
        ]);
        $this->assertDatabaseHas('evaluation_criteria_scores', [
            'evaluation_id' => $evaluation->id,
            'evaluation_criteria_id' => $newCriteria->id,
            'score' => 5,
        ]);
    }

    public function test_update_guest_redirect_login(): void
    {
        $evaluation = Evaluation::factory()->create();
        $player = Player::factory()->create();
        $homeTeam = Club::factory()->create();
        $awayTeam = Club::factory()->create();
        $criteria = EvaluationCriteria::factory()->create();

        $response = $this->put(route('evaluation.update', $evaluation), [
            'player_id' => $player->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'kickoff_date' => '2026-08-01',
            'kickoff_time' => '15:30',
            'criteriaScores' => [
                ['evaluation_criteria_id' => $criteria->id, 'score' => 8],
            ],
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_update_validates_required_fields(): void
    {
        $evaluation = Evaluation::factory()->create(['created_by' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->put(route('evaluation.update', $evaluation), []);

        $response->assertInvalid([
            'player_id',
            'home_team_id',
            'away_team_id',
            'kickoff_date',
            'kickoff_time',
            'criteriaScores',
        ]);
    }
}
