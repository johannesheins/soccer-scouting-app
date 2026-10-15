<?php

namespace Tests\Feature\Evaluation;

use App\Enums\AuditEvent;
use App\Enums\Permission\EvaluationPermissions;
use App\Interfaces\PermissionsInterface;
use App\Models\AuditLog;
use App\Models\Club;
use App\Models\Evaluation;
use App\Models\EvaluationCriteria;
use App\Models\EvaluationCriteriaGroup;
use App\Models\Position;
use App\Models\Recommendation;
use App\Models\User;
use DateTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\TestCase;

abstract class EvaluationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    abstract protected function modelClass(): string;
    abstract protected function routeName(): string;
    abstract protected function permissionEnum(): string;
    abstract protected function componentPrefix(): string;
    abstract protected function typePayload(): array;
    abstract protected function requiredTypeFields(): array;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUserWithRight([
            EvaluationPermissions::Index,
            EvaluationPermissions::Search,
            ...array_map($this->right(...), [
                'Create', 'View', 'ViewAll', 'Edit', 'EditAll', 'Destroy', 'DestroyAll',
            ]),
        ]);
    }

    protected function right(string $case): PermissionsInterface
    {
        return constant($this->permissionEnum() . '::' . $case);
    }

    protected function typeRoute(string $action): string
    {
        return "{$this->routeName()}.{$action}";
    }

    protected function payload(array $overrides = []): array
    {
        return array_replace([
            'date' => '2026-08-01',
            'recommendation_id' => Recommendation::factory()->create()->id,
            'criteriaScores' => [
                ['evaluation_criteria_id' => EvaluationCriteria::factory()->create()->id, 'score' => 8],
            ],
        ], $this->typePayload(), $overrides);
    }

    protected function assertEvaluationMatchesPayload(Evaluation $evaluation, array $payload): void
    {
        $this->assertSame($payload['date'], $evaluation->date->toDateString());

        foreach (Arr::except($payload, ['date', 'criteriaScores']) as $attribute => $value) {
            $this->assertEquals($value, $evaluation->getAttribute($attribute), $attribute);
        }
    }

    protected function assertScoreStored(Evaluation $evaluation, int $criteriaId, int $score): void
    {
        $this->assertDatabaseHas('evaluation_criteria_scores', [
            $evaluation->criteriaScores()->getForeignKeyName() => $evaluation->id,
            'evaluation_criteria_id' => $criteriaId,
            'score' => $score,
        ]);
    }

    public function test_index()
    {
        $response = $this->actingAs($this->user)
            ->get(route('evaluation.index'));

        $response->assertOk();
        $this->assertRights(EvaluationPermissions::Index, 'evaluation.index');
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
            ->get(route($this->typeRoute('create')));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
            ->component("{$this->componentPrefix()}-create")
            ->has('clubs', 10)
            ->has('positions', 8)
            ->has('evaluationCriteriaGroups', 3)
            ->has('evaluationCriteriaGroups.0.evaluation_criteria', 3)
            ->has('recommendations', 4)
        );
        $this->assertRights($this->right('Create'), $this->typeRoute('create'));
    }

    protected function loggedScores(array $values): array
    {
        return array_map(
            fn (array $score) => Arr::only($score, ['evaluation_criteria_id', 'score']),
            $values['criteriaScores'],
        );
    }

    public function test_store_creates_evaluation(): void
    {
        $payload = $this->payload([
            'strengths' => 'Good positioning',
            'weaknesses' => 'Weak on the left foot',
            'comment' => 'Promising talent',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route($this->typeRoute('store')), $payload);

        $response->assertRedirect(route('evaluation.index'));

        $evaluation = $this->modelClass()::sole();
        $this->assertEvaluationMatchesPayload($evaluation, $payload);
        $this->assertSame($this->user->id, $evaluation->created_by);
        $this->assertScoreStored(
            $evaluation,
            $payload['criteriaScores'][0]['evaluation_criteria_id'],
            8,
        );

        $this->assertRights($this->right('Create'), $this->typeRoute('store'));
    }

    public function test_store_creates_a_criteria_score_for_each_entry(): void
    {
        $criteria = EvaluationCriteria::factory(3)->create();

        $response = $this->actingAs($this->user)
            ->post(route($this->typeRoute('store')), $this->payload([
                'criteriaScores' => $criteria->map(fn ($criterion, $index) => [
                    'evaluation_criteria_id' => $criterion->id,
                    'score' => $index + 1,
                ])->all(),
            ]));

        $response->assertRedirect(route('evaluation.index'));
        $evaluation = $this->modelClass()::sole();

        $this->assertSame(3, $evaluation->criteriaScores()->count());
        $criteria->each(fn ($criterion, $index) => $this->assertScoreStored($evaluation, $criterion->id, $index + 1));
    }

    public function test_store_writes_an_audit_log(): void
    {
        $payload = $this->payload();

        $this->actingAs($this->user)
            ->post(route($this->typeRoute('store')), $payload);

        $evaluation = $this->modelClass()::sole();
        $log = $evaluation->auditLogs()->sole();
        $this->assertSame(AuditEvent::CREATED, $log->event);
        $this->assertSame($this->modelClass(), $log->auditable_type);
        $this->assertNull($log->old_values);
        $this->assertEquals($payload['recommendation_id'], $log->new_values['recommendation_id']);
        $this->assertSame($payload['criteriaScores'], $this->loggedScores($log->new_values));
        $this->assertSame($this->user->id, $log->user_id);
    }

    public function test_store_guest_redirect_login(): void
    {
        $response = $this->post(route($this->typeRoute('store')), $this->payload());

        $response->assertRedirect(route('login'));
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route($this->typeRoute('store')), []);

        $response->assertInvalid([
            ...$this->requiredTypeFields(),
            'date',
            'recommendation_id',
            'criteriaScores',
        ]);
    }

    public function test_store_validates_date_is_date(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route($this->typeRoute('store')), $this->payload(['date' => 'not-a-date']));

        $response->assertInvalid(['date']);
    }

    public function test_store_date_can_not_be_in_future(): void
    {
        $tomorrow = (new DateTime('now'))->modify('+1 day')->format('Y-m-d');

        $response = $this->actingAs($this->user)
            ->post(route($this->typeRoute('store')), $this->payload(['date' => $tomorrow]));

        $response->assertInvalid(['date']);
    }

    public function test_store_validates_recommendation_exists(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route($this->typeRoute('store')), $this->payload(['recommendation_id' => 999]));

        $response->assertInvalid(['recommendation_id']);
    }

    public function test_store_validates_strengths_max_length(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route($this->typeRoute('store')), $this->payload(['strengths' => str_repeat('a', 256)]));

        $response->assertInvalid(['strengths']);
    }

    public function test_store_validates_weaknesses_max_length(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route($this->typeRoute('store')), $this->payload(['weaknesses' => str_repeat('a', 256)]));

        $response->assertInvalid(['weaknesses']);
    }

    public function test_store_validates_comment_max_length(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route($this->typeRoute('store')), $this->payload(['comment' => str_repeat('a', 256)]));

        $response->assertInvalid(['comment']);
    }

    public function test_store_validates_criteria_scores_evaluation_criteria_id_exists(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route($this->typeRoute('store')), $this->payload([
                'criteriaScores' => [['evaluation_criteria_id' => 999, 'score' => 8]],
            ]));

        $response->assertInvalid(['criteriaScores.0.evaluation_criteria_id']);
    }

    public function test_store_validates_criteria_scores_score_is_numeric(): void
    {
        $criteria = EvaluationCriteria::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route($this->typeRoute('store')), $this->payload([
                'criteriaScores' => [['evaluation_criteria_id' => $criteria->id, 'score' => 'not-a-number']],
            ]));

        $response->assertInvalid(['criteriaScores.0.score']);
    }

    public function test_store_validates_criteria_scores_score_within_range(): void
    {
        $criteria = EvaluationCriteria::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route($this->typeRoute('store')), $this->payload([
                'criteriaScores' => [['evaluation_criteria_id' => $criteria->id, 'score' => 11]],
            ]));

        $response->assertInvalid(['criteriaScores.0.score']);
    }

    public function test_edit_view()
    {
        Club::factory(10)->create();
        Position::factory(8)->create();

        foreach (EvaluationCriteriaGroup::factory(3)->create() as $criteriaGroup) {
            EvaluationCriteria::factory(3)->create(['evaluation_criteria_group_id' => $criteriaGroup->id]);
        }

        Recommendation::factory(4)->create();
        $evaluation = $this->modelClass()::factory()->create(['created_by' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route($this->typeRoute('edit'), $evaluation));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
            ->component("{$this->componentPrefix()}-edit")
            ->where('evaluation.id', $evaluation->id)
            // the evaluation factory creates further clubs / positions / recommendations
            ->has('clubs', Club::count())
            ->has('positions', Position::count())
            ->has('evaluationCriteriaGroups', 3)
            ->has('evaluationCriteriaGroups.0.evaluation_criteria', 3)
            ->has('recommendations', Recommendation::count())
        );
        $this->assertRights($this->right('EditAll'), [$this->typeRoute('edit'), $evaluation]);
    }

    public function test_edit_guest_redirect_login(): void
    {
        $evaluation = $this->modelClass()::factory()->create();

        $response = $this->get(route($this->typeRoute('edit'), $evaluation));

        $response->assertRedirect(route('login'));
    }

    public function test_update_updates_evaluation(): void
    {
        $evaluation = $this->modelClass()::factory()->create(['created_by' => $this->user->id]);
        $payload = $this->payload([
            'strengths' => 'Good positioning',
            'weaknesses' => 'Weak on the left foot',
            'comment' => 'Promising talent',
        ]);

        $response = $this->actingAs($this->user)
            ->put(route($this->typeRoute('update'), $evaluation), $payload);

        $response->assertRedirect(route('evaluation.index'));

        $evaluation->refresh();
        $this->assertEvaluationMatchesPayload($evaluation, $payload);
        $this->assertScoreStored(
            $evaluation,
            $payload['criteriaScores'][0]['evaluation_criteria_id'],
            8,
        );

        $this->assertRights($this->right('EditAll'), [$this->typeRoute('update'), $evaluation]);
    }

    public function test_update_replaces_criteria_scores(): void
    {
        $evaluation = $this->modelClass()::factory()->create(['created_by' => $this->user->id]);
        $oldCriteria = EvaluationCriteria::factory()->create();
        $newCriteria = EvaluationCriteria::factory()->create();
        $evaluation->criteriaScores()->create(['evaluation_criteria_id' => $oldCriteria->id, 'score' => 3]);

        $response = $this->actingAs($this->user)
            ->put(route($this->typeRoute('update'), $evaluation), $this->payload([
                'criteriaScores' => [['evaluation_criteria_id' => $newCriteria->id, 'score' => 5]],
            ]));

        $response->assertRedirect(route('evaluation.index'));
        $this->assertSame(1, $evaluation->criteriaScores()->count());
        $this->assertDatabaseMissing('evaluation_criteria_scores', [
            $evaluation->criteriaScores()->getForeignKeyName() => $evaluation->id,
            'evaluation_criteria_id' => $oldCriteria->id,
        ]);
        $this->assertScoreStored($evaluation, $newCriteria->id, 5);
    }

    public function test_update_writes_an_audit_log(): void
    {
        $evaluation = $this->modelClass()::factory()->create(['created_by' => $this->user->id]);
        $oldCriteria = EvaluationCriteria::factory()->create();
        $evaluation->criteriaScores()->create(['evaluation_criteria_id' => $oldCriteria->id, 'score' => 3]);
        $payload = $this->payload(['strengths' => 'Good positioning']);

        $this->actingAs($this->user)
            ->put(route($this->typeRoute('update'), $evaluation), $payload);

        $log = $evaluation->auditLogs()->sole();
        $this->assertSame(AuditEvent::UPDATED, $log->event);
        $this->assertSame($evaluation->getOriginal('strengths'), $log->old_values['strengths']);
        $this->assertSame('Good positioning', $log->new_values['strengths']);
        $this->assertSame(
            [['evaluation_criteria_id' => $oldCriteria->id, 'score' => 3]],
            $this->loggedScores($log->old_values),
        );
        $this->assertSame($payload['criteriaScores'], $this->loggedScores($log->new_values));
        $this->assertSame($this->user->id, $log->user_id);
    }

    public function test_update_guest_redirect_login(): void
    {
        $evaluation = $this->modelClass()::factory()->create();

        $response = $this->put(route($this->typeRoute('update'), $evaluation), $this->payload());

        $response->assertRedirect(route('login'));
    }

    public function test_update_validates_required_fields(): void
    {
        $evaluation = $this->modelClass()::factory()->create(['created_by' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->put(route($this->typeRoute('update'), $evaluation), []);

        $response->assertInvalid([
            ...$this->requiredTypeFields(),
            'date',
            'criteriaScores',
            'recommendation_id',
        ]);
    }

    public function test_show_guest_redirect_login(): void
    {
        $evaluation = $this->modelClass()::factory()->create();

        $this->get(route($this->typeRoute('show'), $evaluation))
            ->assertRedirect(route('login'));
    }

    public function test_show_renders_modal(): void
    {
        $evaluation = $this->modelClass()::factory()->create(['created_by' => $this->user->id]);
        $evaluation->criteriaScores()->create([
            'evaluation_criteria_id' => EvaluationCriteria::factory()->create()->id,
            'score' => 6,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route($this->typeRoute('show'), $evaluation));

        $response->assertInertia(
            fn ($page) => $page
            ->component('evaluation/evaluation-index')
            ->where('modal.component', 'evaluation/evaluation-show')
            ->where('modal.props.evaluation.id', $evaluation->id)
            ->where('modal.props.evaluation.evaluation_type', $evaluation->evaluation_type)
            ->has('modal.props.evaluation.criteria_scores', 1)
            ->has('modal.props.evaluation.recommendation')
            ->has('modal.props.evaluation.creator')
            ->has('modal.props.evaluation.player')
        );

        $this->assertRights($this->right('ViewAll'), [$this->typeRoute('show'), $evaluation]);
    }

    public function test_show_returns_404_for_nonexistent_evaluation(): void
    {
        $this->actingAs($this->user)
            ->get(route($this->typeRoute('show'), 999))
            ->assertNotFound();
    }

    public function test_destroy_deletes_evaluation_and_its_scores(): void
    {
        $evaluation = $this->modelClass()::factory()->create(['created_by' => $this->user->id]);
        $evaluation->criteriaScores()->create([
            'evaluation_criteria_id' => EvaluationCriteria::factory()->create()->id,
            'score' => 6,
        ]);

        $this->assertRights($this->right('DestroyAll'), [$this->typeRoute('destroy'), $evaluation]);

        $response = $this->actingAs($this->user)
            ->delete(route($this->typeRoute('destroy'), $evaluation));

        $response->assertRedirect(route('evaluation.index'));
        $this->assertModelMissing($evaluation);
        $this->assertDatabaseMissing('evaluation_criteria_scores', [
            $evaluation->criteriaScores()->getForeignKeyName() => $evaluation->id,
        ]);
    }

    public function test_destroy_writes_an_audit_log(): void
    {
        $evaluation = $this->modelClass()::factory()->create(['created_by' => $this->user->id]);
        $criteria = EvaluationCriteria::factory()->create();
        $evaluation->criteriaScores()->create(['evaluation_criteria_id' => $criteria->id, 'score' => 6]);

        $this->actingAs($this->user)
            ->delete(route($this->typeRoute('destroy'), $evaluation));

        $log = AuditLog::where('auditable_type', $this->modelClass())
            ->where('auditable_id', $evaluation->id)
            ->sole();
        $this->assertSame(AuditEvent::DELETED, $log->event);
        $this->assertSame($evaluation->id, $log->old_values['id']);
        $this->assertSame(
            [['evaluation_criteria_id' => $criteria->id, 'score' => 6]],
            $this->loggedScores($log->old_values),
        );
        $this->assertNull($log->new_values);
        $this->assertSame($this->user->id, $log->user_id);
    }

    public function test_destroy_guest_redirect_login(): void
    {
        $evaluation = $this->modelClass()::factory()->create();

        $this->delete(route($this->typeRoute('destroy'), $evaluation))
            ->assertRedirect(route('login'));
    }

    public function test_destroy_returns_404_for_nonexistent_evaluation(): void
    {
        $this->actingAs($this->user)
            ->delete(route($this->typeRoute('destroy'), 999))
            ->assertNotFound();
    }

    public function test_history_requires_the_view_history_right(): void
    {
        $evaluation = $this->modelClass()::factory()->create();
        $user = $this->createUserWithRight([$this->right('ViewHistory'), $this->right('ViewAll')]);

        $this->actingAs($user)
            ->get(route($this->typeRoute('history'), $evaluation))
            ->assertOk();

        $this->actingAs(User::factory()->administrator()->create())
            ->get(route($this->typeRoute('history'), $evaluation))
            ->assertOk();
    }

    public function test_history_is_forbidden_with_view_history_but_without_view(): void
    {
        $evaluation = $this->modelClass()::factory()->create();
        $user = $this->createUserWithRight([$this->right('ViewHistory')]);

        $this->actingAs($user)
            ->get(route($this->typeRoute('history'), $evaluation))
            ->assertForbidden();
    }

    public function test_history_with_view_only_allows_own_evaluations(): void
    {
        $user = $this->createUserWithRight([$this->right('ViewHistory'), $this->right('View')]);
        $own = $this->modelClass()::factory()->create(['created_by' => $user->id]);
        $foreign = $this->modelClass()::factory()->create();

        $this->actingAs($user)
            ->get(route($this->typeRoute('history'), $own))
            ->assertOk();

        $this->actingAs($user)
            ->get(route($this->typeRoute('history'), $foreign))
            ->assertForbidden();
    }

    public function test_history_is_forbidden_with_view_all_but_without_view_history(): void
    {
        $evaluation = $this->modelClass()::factory()->create(['created_by' => $this->user->id]);

        $this->actingAs($this->user)
            ->get(route($this->typeRoute('history'), $evaluation))
            ->assertForbidden();
    }

    public function test_history_renders_modal_with_versions(): void
    {
        $user = $this->createUserWithRight([$this->right('ViewHistory'), $this->right('ViewAll')]);
        $evaluation = $this->modelClass()::factory()->create();
        $evaluation->saveLog(AuditEvent::CREATED, null, $evaluation->createSnapshot());

        $response = $this->actingAs($user)
            ->get(route($this->typeRoute('history'), $evaluation));

        $response->assertInertia(
            fn ($page) => $page
            ->component('evaluation/evaluation-index')
            ->where('modal.component', 'evaluation/evaluation-view-history')
            ->has('modal.props.evaluationCriteriaGroups')
            ->has('modal.props.versions', 1)
            ->where('modal.props.versions.0.event', AuditEvent::CREATED->value)
            ->where('modal.props.versions.0.evaluation.id', $evaluation->id)
            ->has('modal.props.versions.0.evaluation.recommendation')
            ->has('modal.props.versions.0.evaluation.creator')
            ->has('modal.props.versions.0.evaluation.player')
        );
    }

    public function test_history_guest_redirect_login(): void
    {
        $evaluation = $this->modelClass()::factory()->create();

        $this->get(route($this->typeRoute('history'), $evaluation))
            ->assertRedirect(route('login'));
    }

    public function test_history_returns_404_for_nonexistent_evaluation(): void
    {
        $this->actingAs(User::factory()->administrator()->create())
            ->get(route($this->typeRoute('history'), 999))
            ->assertNotFound();
    }

    public function test_history_renders_every_version_in_chronological_order(): void
    {
        $evaluation = $this->modelClass()::factory()->create();
        $evaluation->saveLog(AuditEvent::CREATED, null, $evaluation->createSnapshot());
        $old = $evaluation->createSnapshot();
        $evaluation->update(['comment' => 'Changed comment']);
        $evaluation->saveLog(AuditEvent::UPDATED, $old, $evaluation->createSnapshot());

        $response = $this->actingAs(User::factory()->administrator()->create())
            ->get(route($this->typeRoute('history'), $evaluation));

        $response->assertInertia(
            fn ($page) => $page
            ->has('modal.props.versions', 2)
            ->where('modal.props.versions.0.event', AuditEvent::CREATED->value)
            ->where('modal.props.versions.0.changes', [])
            ->where('modal.props.versions.1.event', AuditEvent::UPDATED->value)
            ->where('modal.props.versions.1.changes', ['comment'])
            ->where('modal.props.versions.1.evaluation.comment', 'Changed comment')
        );
    }
}
