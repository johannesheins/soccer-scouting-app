<?php

namespace Tests\Feature\Evaluation;

use App\Enums\AuditEvent;
use App\Models\Club;
use App\Models\EvaluationCriteria;
use App\Models\GameEvaluation;
use App\Models\Player;
use App\Models\PlayerEvaluation;
use App\Models\Recommendation;
use App\Models\User;
use App\Services\EvaluationService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
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
            'home_club_id' => Club::factory()->create()->id,
            'home_team' => 'U19',
            'guest_club_id' => Club::factory()->create()->id,
            'guest_team' => 'U19 II',
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
        $this->assertSame($data['home_club_id'], $stored->home_club_id);
        $this->assertSame($data['home_team'], $stored->home_team);
        $this->assertSame($data['guest_club_id'], $stored->guest_club_id);
        $this->assertSame($data['guest_team'], $stored->guest_team);
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
        $this->assertSame($data['home_club_id'], $stored->home_club_id);
        $this->assertSame($data['home_team'], $stored->home_team);
        $this->assertSame($data['guest_club_id'], $stored->guest_club_id);
        $this->assertSame($data['guest_team'], $stored->guest_team);
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

    // audit log
    public function test_create_logs_the_criteria_scores(): void
    {
        $this->actingAs($this->user);
        $data = $this->evaluationData();
        $criteriaId = $data['criteriaScores'][0]['evaluation_criteria_id'];

        $evaluation = EvaluationService::create(new GameEvaluation(), $data);

        $log = $evaluation->auditLogs()->sole();
        $this->assertSame(AuditEvent::CREATED, $log->event);
        $this->assertNull($log->old_values);
        $this->assertSame(
            [['player_evaluation_id' => null, 'evaluation_criteria_id' => $criteriaId, 'score' => 8]],
            $log->new_values['criteriaScores']
        );
        $this->assertSame($this->user->id, $log->user_id);
    }

    public function test_update_logs_the_old_and_new_criteria_scores(): void
    {
        $this->actingAs($this->user);
        $evaluation = GameEvaluation::factory()->create();
        $oldCriteria = EvaluationCriteria::factory()->create();
        $newCriteria = EvaluationCriteria::factory()->create();
        $evaluation->criteriaScores()->create(['evaluation_criteria_id' => $oldCriteria->id, 'score' => 3]);

        EvaluationService::update($evaluation, $this->evaluationData([
            'criteriaScores' => [['evaluation_criteria_id' => $newCriteria->id, 'score' => 5]],
        ]));

        $log = $evaluation->auditLogs()->where('event', AuditEvent::UPDATED)->sole();
        $this->assertSame(
            [['player_evaluation_id' => null, 'evaluation_criteria_id' => $oldCriteria->id, 'score' => 3]],
            $log->old_values['criteriaScores']
        );
        $this->assertSame(
            [['player_evaluation_id' => null, 'evaluation_criteria_id' => $newCriteria->id, 'score' => 5]],
            $log->new_values['criteriaScores']
        );
    }

    public function test_delete_logs_the_deleted_evaluation_with_its_scores(): void
    {
        $this->actingAs($this->user);
        $evaluation = GameEvaluation::factory()->create();
        $criteria = EvaluationCriteria::factory()->create();
        $evaluation->criteriaScores()->create(['evaluation_criteria_id' => $criteria->id, 'score' => 6]);

        $this->assertTrue(EvaluationService::delete($evaluation));

        $this->assertModelMissing($evaluation);
        $log = $evaluation->auditLogs()->sole();
        $this->assertSame(AuditEvent::DELETED, $log->event);
        $this->assertSame($evaluation->id, $log->old_values['id']);
        $this->assertSame(
            [['player_evaluation_id' => null, 'evaluation_criteria_id' => $criteria->id, 'score' => 6]],
            $log->old_values['criteriaScores']
        );
        $this->assertNull($log->new_values);
        $this->assertSame($this->user->id, $log->user_id);
    }

    // versions
    public function test_versions_rebuilds_every_logged_state_of_the_evaluation(): void
    {
        $this->actingAs($this->user);
        $oldCriteria = EvaluationCriteria::factory()->create();
        $newCriteria = EvaluationCriteria::factory()->create();
        $evaluation = EvaluationService::create(new GameEvaluation(), $this->evaluationData([
            'strengths' => 'Old strengths',
            'criteriaScores' => [['evaluation_criteria_id' => $oldCriteria->id, 'score' => 3]],
        ]));
        EvaluationService::update($evaluation, $this->evaluationData([
            'strengths' => 'New strengths',
            'criteriaScores' => [['evaluation_criteria_id' => $newCriteria->id, 'score' => 7]],
        ]));

        $versions = EvaluationService::versions($evaluation);

        $this->assertCount(2, $versions);
        $this->assertSame(AuditEvent::CREATED, $versions[0]['event']);
        $this->assertSame('Old strengths', $versions[0]['evaluation']->strengths);
        $this->assertSame(
            [['evaluation_criteria_id' => $oldCriteria->id, 'score' => 3]],
            $versions[0]['evaluation']->criteriaScores->map->only('evaluation_criteria_id', 'score')->all()
        );
        $this->assertSame(AuditEvent::UPDATED, $versions[1]['event']);
        $this->assertSame('New strengths', $versions[1]['evaluation']->strengths);
        $this->assertSame(
            [['evaluation_criteria_id' => $newCriteria->id, 'score' => 7]],
            $versions[1]['evaluation']->criteriaScores->map->only('evaluation_criteria_id', 'score')->all()
        );
        $this->assertTrue($versions[1]['user']->is($this->user));
    }

    public function test_versions_loads_the_relations_of_the_logged_state(): void
    {
        $this->actingAs($this->user);
        $oldRecommendation = Recommendation::factory()->create();
        $oldHomeClub = Club::factory()->create();
        $evaluation = EvaluationService::create(new GameEvaluation(), $this->evaluationData([
            'recommendation_id' => $oldRecommendation->id,
            'home_club_id' => $oldHomeClub->id,
        ]));
        EvaluationService::update($evaluation, $this->evaluationData());

        $versions = EvaluationService::versions($evaluation);

        $this->assertTrue($versions[0]['evaluation']->recommendation->is($oldRecommendation));
        $this->assertTrue($versions[0]['evaluation']->homeClub->is($oldHomeClub));
        $this->assertTrue($versions[0]['evaluation']->creator->is($this->user));
    }

    public function test_versions_lists_the_changed_fields(): void
    {
        $this->actingAs($this->user);
        $data = $this->evaluationData();
        $evaluation = EvaluationService::create(new GameEvaluation(), $data);
        EvaluationService::update($evaluation, [
            ...$data,
            'comment' => 'Changed comment',
            'criteriaScores' => [['evaluation_criteria_id' => $data['criteriaScores'][0]['evaluation_criteria_id'], 'score' => 2]],
        ]);

        $versions = EvaluationService::versions($evaluation);

        $this->assertSame([], $versions[0]['changes']);
        $this->assertEqualsCanonicalizing(['comment', 'criteriaScores'], $versions[1]['changes']);
    }

    public function test_versions_returns_an_empty_array_without_audit_logs(): void
    {
        $evaluation = GameEvaluation::factory()->create();

        $this->assertSame([], EvaluationService::versions($evaluation));
    }

    public function test_versions_are_ordered_chronologically(): void
    {
        $this->actingAs($this->user);
        $evaluation = EvaluationService::create(new GameEvaluation(), $this->evaluationData(['comment' => 'First']));
        EvaluationService::update($evaluation, $this->evaluationData(['comment' => 'Second']));
        EvaluationService::update($evaluation, $this->evaluationData(['comment' => 'Third']));

        $versions = EvaluationService::versions($evaluation);

        $this->assertSame(['First', 'Second', 'Third'], array_map(fn (array $version) => $version['evaluation']->comment, $versions));
        $this->assertSame($evaluation->auditLogs()->orderBy('id')->pluck('id')->all(), array_column($versions, 'id'));
    }

    public function test_versions_rebuilds_the_deleted_state_from_the_old_values(): void
    {
        $this->actingAs($this->user);
        $evaluation = EvaluationService::create(new GameEvaluation(), $this->evaluationData(['comment' => 'Before deletion']));
        EvaluationService::delete($evaluation);

        $versions = EvaluationService::versions($evaluation);

        $this->assertCount(2, $versions);
        $this->assertSame(AuditEvent::DELETED, $versions[1]['event']);
        $this->assertNull($versions[1]['new_values']);
        $this->assertSame([], $versions[1]['changes']);
        $this->assertSame('Before deletion', $versions[1]['evaluation']->comment);
        $this->assertCount(1, $versions[1]['evaluation']->criteriaScores);
    }

    public function test_versions_does_not_list_unchanged_fields_or_updated_at(): void
    {
        $this->actingAs($this->user);
        $evaluation = EvaluationService::create(new GameEvaluation(), $this->evaluationData());
        $snapshot = $evaluation->createSnapshot();
        $evaluation->saveLog(
            AuditEvent::UPDATED,
            [...$snapshot, 'updated_at' => '2026-01-01 00:00:00'],
            [...$snapshot, 'updated_at' => '2026-02-01 00:00:00']
        );

        $versions = EvaluationService::versions($evaluation);

        $this->assertSame([], $versions[1]['changes']);
    }

    public function test_versions_returns_the_user_who_made_each_change(): void
    {
        $editor = User::factory()->create();
        $this->actingAs($this->user);
        $evaluation = EvaluationService::create(new GameEvaluation(), $this->evaluationData());
        $this->actingAs($editor);
        EvaluationService::update($evaluation, $this->evaluationData());
        $editor->delete();

        $versions = EvaluationService::versions($evaluation);

        $this->assertTrue($versions[0]['user']->is($this->user));
        $this->assertTrue($versions[1]['user']->is($editor));
    }

    public function test_versions_does_not_modify_the_evaluation_or_persist_anything(): void
    {
        $this->actingAs($this->user);
        $evaluation = EvaluationService::create(new GameEvaluation(), $this->evaluationData(['strengths' => 'Old strengths']));
        EvaluationService::update($evaluation, $this->evaluationData(['strengths' => 'New strengths']));
        $scoreCount = DB::table('evaluation_criteria_scores')->count();

        $versions = EvaluationService::versions($evaluation);

        $this->assertNotSame($evaluation, $versions[0]['evaluation']);
        $this->assertSame('New strengths', $evaluation->strengths);
        $this->assertSame('New strengths', $evaluation->fresh()->strengths);
        $this->assertSame($scoreCount, DB::table('evaluation_criteria_scores')->count());
        $this->assertSame(2, $evaluation->auditLogs()->count());
    }

    public function test_versions_of_a_player_evaluation_load_the_player(): void
    {
        $this->actingAs($this->user);
        $oldPlayer = Player::factory()->create();
        $evaluation = EvaluationService::create(new PlayerEvaluation(), [
            ...Arr::only($this->evaluationData(), ['date', 'recommendation_id', 'criteriaScores']),
            'player_id' => $oldPlayer->id,
        ]);
        EvaluationService::update($evaluation, [
            ...Arr::only($this->evaluationData(), ['date', 'recommendation_id', 'criteriaScores']),
            'player_id' => Player::factory()->create()->id,
        ]);

        $versions = EvaluationService::versions($evaluation);

        $this->assertInstanceOf(PlayerEvaluation::class, $versions[0]['evaluation']);
        $this->assertTrue($versions[0]['evaluation']->player->is($oldPlayer));
        $this->assertContains('player_id', $versions[1]['changes']);
    }
}
