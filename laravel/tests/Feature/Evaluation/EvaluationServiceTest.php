<?php

namespace Tests\Feature\Evaluation;

use App\Models\Club;
use App\Models\EvaluationCriteria;
use App\Models\GameEvaluation;
use App\Models\Player;
use App\Models\Recommendation;
use App\Models\User;
use App\Services\EvaluationService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    private function evaluationData(array $overrides = []): array
    {
        return array_replace([
            'date' => '2026-08-01',
            'strengths' => 'Good positioning',
            'weaknesses' => 'Weak on the left foot',
            'comment' => 'Promising talent',
            'recommendation_id' => Recommendation::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'home_team_id' => Club::factory()->create()->id,
            'guest_team_id' => Club::factory()->create()->id,
            'criteriaScores' => [
                ['evaluation_criteria_id' => EvaluationCriteria::factory()->create()->id, 'score' => 8],
            ],
        ], $overrides);
    }

    private function assertScoreStored(GameEvaluation $evaluation, int $criteriaId, int $score): void
    {
        $this->assertDatabaseHas('evaluation_criteria_scores', [
            'game_evaluation_id' => $evaluation->id,
            'evaluation_criteria_id' => $criteriaId,
            'score' => $score,
        ]);
    }

    // create
    public function test_create_persists_the_evaluation_with_the_given_attributes(): void
    {
        $this->actingAs($this->user);
        $data = $this->evaluationData();

        $evaluation = EvaluationService::create(new GameEvaluation(), $data);

        $this->assertTrue($evaluation->exists);
        $this->assertSame(1, GameEvaluation::count());

        $stored = GameEvaluation::sole();
        $this->assertSame('2026-08-01', $stored->date->toDateString());
        $this->assertSame('Good positioning', $stored->strengths);
        $this->assertSame('Weak on the left foot', $stored->weaknesses);
        $this->assertSame('Promising talent', $stored->comment);
        $this->assertSame($data['recommendation_id'], $stored->recommendation_id);
        $this->assertSame($data['player_id'], $stored->player_id);
        $this->assertSame($data['home_team_id'], $stored->home_team_id);
        $this->assertSame($data['guest_team_id'], $stored->guest_team_id);
    }

    public function test_create_returns_the_given_evaluation_instance(): void
    {
        $this->actingAs($this->user);
        $evaluation = new GameEvaluation();

        $result = EvaluationService::create($evaluation, $this->evaluationData());

        $this->assertSame($evaluation, $result);
    }

    public function test_create_sets_created_by_to_the_authenticated_user(): void
    {
        $this->actingAs($this->user);

        $evaluation = EvaluationService::create(new GameEvaluation(), $this->evaluationData());

        $this->assertSame($this->user->id, $evaluation->created_by);
        $this->assertSame($this->user->id, GameEvaluation::sole()->created_by);
    }

    public function test_create_ignores_a_created_by_passed_in_the_data(): void
    {
        $this->actingAs($this->user);
        $otherUser = User::factory()->create();

        $evaluation = EvaluationService::create(
            new GameEvaluation(),
            $this->evaluationData(['created_by' => $otherUser->id]),
        );

        $this->assertSame($this->user->id, $evaluation->created_by);
    }

    public function test_create_stores_a_criteria_score_for_each_entry(): void
    {
        $this->actingAs($this->user);
        $criteria = EvaluationCriteria::factory(3)->create();

        $evaluation = EvaluationService::create(new GameEvaluation(), $this->evaluationData([
            'criteriaScores' => $criteria->map(fn ($criterion, $index) => [
                'evaluation_criteria_id' => $criterion->id,
                'score' => $index + 1,
            ])->all(),
        ]));

        $this->assertSame(3, $evaluation->criteriaScores()->count());
        $criteria->each(fn ($criterion, $index) => $this->assertScoreStored($evaluation, $criterion->id, $index + 1));
    }

    public function test_create_does_not_treat_criteria_scores_as_an_evaluation_attribute(): void
    {
        $this->actingAs($this->user);

        $evaluation = EvaluationService::create(new GameEvaluation(), $this->evaluationData());

        $this->assertArrayNotHasKey('criteriaScores', $evaluation->getAttributes());
    }

    public function test_create_rolls_back_the_evaluation_when_storing_the_scores_fails(): void
    {
        $this->actingAs($this->user);

        try {
            EvaluationService::create(new GameEvaluation(), $this->evaluationData([
                'criteriaScores' => [['evaluation_criteria_id' => 999, 'score' => 8]],
            ]));
            $this->fail('Expected a QueryException for the unknown evaluation criteria.');
        } catch (QueryException) {
            $this->assertSame(0, GameEvaluation::count());
            $this->assertDatabaseCount('evaluation_criteria_scores', 0);
        }
    }

    // update
    public function test_update_changes_the_evaluation_attributes(): void
    {
        $evaluation = GameEvaluation::factory()->create(['created_by' => $this->user->id]);
        $data = $this->evaluationData([
            'strengths' => 'Strong in the air',
            'weaknesses' => 'Slow turn',
            'comment' => 'Worth a second look',
        ]);

        EvaluationService::update($evaluation, $data);

        $stored = $evaluation->fresh();
        $this->assertSame('2026-08-01', $stored->date->toDateString());
        $this->assertSame('Strong in the air', $stored->strengths);
        $this->assertSame('Slow turn', $stored->weaknesses);
        $this->assertSame('Worth a second look', $stored->comment);
        $this->assertSame($data['recommendation_id'], $stored->recommendation_id);
        $this->assertSame($data['player_id'], $stored->player_id);
        $this->assertSame($data['home_team_id'], $stored->home_team_id);
        $this->assertSame($data['guest_team_id'], $stored->guest_team_id);
    }

    public function test_update_returns_the_given_evaluation_instance(): void
    {
        $evaluation = GameEvaluation::factory()->create();

        $result = EvaluationService::update($evaluation, $this->evaluationData());

        $this->assertSame($evaluation, $result);
    }

    public function test_update_keeps_the_original_creator(): void
    {
        $creator = User::factory()->create();
        $evaluation = GameEvaluation::factory()->create(['created_by' => $creator->id]);

        $this->actingAs($this->user);
        EvaluationService::update($evaluation, $this->evaluationData());

        $this->assertSame($creator->id, $evaluation->fresh()->created_by);
    }

    public function test_update_ignores_a_created_by_passed_in_the_data(): void
    {
        $creator = User::factory()->create();
        $otherUser = User::factory()->create();
        $evaluation = GameEvaluation::factory()->create(['created_by' => $creator->id]);

        EvaluationService::update($evaluation, $this->evaluationData(['created_by' => $otherUser->id]));

        $this->assertSame($creator->id, $evaluation->fresh()->created_by);
    }

    public function test_update_replaces_the_criteria_scores(): void
    {
        $evaluation = GameEvaluation::factory()->create();
        $oldCriteria = EvaluationCriteria::factory()->create();
        $newCriteria = EvaluationCriteria::factory()->create();
        $evaluation->criteriaScores()->create(['evaluation_criteria_id' => $oldCriteria->id, 'score' => 3]);

        EvaluationService::update($evaluation, $this->evaluationData([
            'criteriaScores' => [['evaluation_criteria_id' => $newCriteria->id, 'score' => 5]],
        ]));

        $this->assertSame(1, $evaluation->criteriaScores()->count());
        $this->assertDatabaseMissing('evaluation_criteria_scores', [
            'game_evaluation_id' => $evaluation->id,
            'evaluation_criteria_id' => $oldCriteria->id,
        ]);
        $this->assertScoreStored($evaluation, $newCriteria->id, 5);
    }

    public function test_update_can_rescore_an_existing_criterion(): void
    {
        $evaluation = GameEvaluation::factory()->create();
        $criteria = EvaluationCriteria::factory()->create();
        $evaluation->criteriaScores()->create(['evaluation_criteria_id' => $criteria->id, 'score' => 3]);

        // the unique (evaluation, criteria) index must not trip over the old row
        EvaluationService::update($evaluation, $this->evaluationData([
            'criteriaScores' => [['evaluation_criteria_id' => $criteria->id, 'score' => 9]],
        ]));

        $this->assertSame(1, $evaluation->criteriaScores()->count());
        $this->assertScoreStored($evaluation, $criteria->id, 9);
    }

    public function test_update_does_not_touch_the_scores_of_other_evaluations(): void
    {
        $evaluation = GameEvaluation::factory()->create();
        $other = GameEvaluation::factory()->create();
        $criteria = EvaluationCriteria::factory()->create();
        $other->criteriaScores()->create(['evaluation_criteria_id' => $criteria->id, 'score' => 4]);

        EvaluationService::update($evaluation, $this->evaluationData());

        $this->assertScoreStored($other, $criteria->id, 4);
    }

    public function test_update_rolls_back_the_evaluation_and_old_scores_when_storing_the_scores_fails(): void
    {
        $evaluation = GameEvaluation::factory()->create(['strengths' => 'Original strengths']);
        $criteria = EvaluationCriteria::factory()->create();
        $evaluation->criteriaScores()->create(['evaluation_criteria_id' => $criteria->id, 'score' => 3]);

        try {
            EvaluationService::update($evaluation, $this->evaluationData([
                'strengths' => 'Changed strengths',
                'criteriaScores' => [['evaluation_criteria_id' => 999, 'score' => 8]],
            ]));
            $this->fail('Expected a QueryException for the unknown evaluation criteria.');
        } catch (QueryException) {
            $this->assertSame('Original strengths', $evaluation->fresh()->strengths);
            $this->assertSame(1, $evaluation->criteriaScores()->count());
            $this->assertScoreStored($evaluation, $criteria->id, 3);
        }
    }
}
