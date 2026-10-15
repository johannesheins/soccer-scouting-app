<?php

namespace Tests\Feature;

use App\Enums\AuditEvent;
use App\Models\AuditLog;
use App\Models\EvaluationCriteria;
use App\Models\GameEvaluation;
use App\Models\Player;
use App\Models\Position;
use App\Models\User;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditableTest extends TestCase
{
    use RefreshDatabase;

    // createSnapshot
    public function test_snapshot_contains_the_model_attributes(): void
    {
        $player = Player::factory()->create(['firstname' => 'John', 'height' => 180]);

        $snapshot = $player->createSnapshot();

        $this->assertSame($player->id, $snapshot['id']);
        $this->assertSame('John', $snapshot['firstname']);
        $this->assertEquals(180, $snapshot['height']);
    }

    public function test_snapshot_excludes_hidden_attributes(): void
    {
        $player = Player::factory()->create();
        $player->setHidden(['height']);

        $snapshot = $player->createSnapshot();

        $this->assertArrayNotHasKey('height', $snapshot);
        $this->assertArrayHasKey('firstname', $snapshot);
    }

    public function test_snapshot_without_audited_relations_contains_only_attributes(): void
    {
        $player = Player::factory()->create();
        $model = new class () extends Model {
            use Auditable;

            protected $table = 'players';
            public $timestamps = false;
        };

        $snapshot = $model->newQuery()->findOrFail($player->id)->createSnapshot();

        $this->assertEqualsCanonicalizing(array_keys($player->getAttributes()), array_keys($snapshot));
    }

    public function test_snapshot_stores_belongs_to_many_relations_as_sorted_ids(): void
    {
        $player = Player::factory()->create();
        $positions = Position::factory(3)->create();
        $player->positions()->attach($positions->pluck('id')->reverse()->all());

        $snapshot = $player->createSnapshot();

        $this->assertSame($positions->pluck('id')->sort()->values()->all(), $snapshot['positions']);
    }

    public function test_snapshot_stores_an_empty_belongs_to_many_relation_as_empty_array(): void
    {
        $player = Player::factory()->create();

        $this->assertSame([], $player->createSnapshot()['positions']);
    }

    public function test_snapshot_stores_has_many_relations_as_attributes_without_keys_and_timestamps(): void
    {
        $evaluation = GameEvaluation::factory()->create();
        $criteria = EvaluationCriteria::factory()->create();
        $evaluation->criteriaScores()->create(['evaluation_criteria_id' => $criteria->id, 'score' => 7]);

        $snapshot = $evaluation->createSnapshot();

        $this->assertSame(
            [['player_evaluation_id' => null, 'evaluation_criteria_id' => $criteria->id, 'score' => 7]],
            $snapshot['criteriaScores']
        );
    }

    public function test_snapshot_orders_has_many_relations_independently_of_insertion_order(): void
    {
        $criteria = EvaluationCriteria::factory(3)->create();
        $first = GameEvaluation::factory()->create();
        $second = GameEvaluation::factory()->create();

        foreach ($criteria as $criterion) {
            $first->criteriaScores()->create(['evaluation_criteria_id' => $criterion->id, 'score' => 5]);
        }
        foreach ($criteria->reverse() as $criterion) {
            $second->criteriaScores()->create(['evaluation_criteria_id' => $criterion->id, 'score' => 5]);
        }

        $this->assertSame(
            $first->createSnapshot()['criteriaScores'],
            $second->createSnapshot()['criteriaScores']
        );
    }

    public function test_snapshot_reloads_relations_that_changed_after_being_loaded(): void
    {
        $player = Player::factory()->create();
        $oldPosition = Position::factory()->create();
        $newPosition = Position::factory()->create();
        $player->positions()->attach($oldPosition);
        $player->load('positions');

        $player->positions()->sync([$newPosition->id]);

        $this->assertSame([$newPosition->id], $player->createSnapshot()['positions']);
    }

    // saveLog
    public function test_save_log_writes_an_audit_log_for_the_model(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $player = Player::factory()->create();

        $player->saveLog(AuditEvent::UPDATED, ['firstname' => 'Old'], ['firstname' => 'New']);

        $log = AuditLog::sole();
        $this->assertSame(Player::class, $log->auditable_type);
        $this->assertSame($player->id, $log->auditable_id);
        $this->assertSame($user->id, $log->user_id);
        $this->assertSame(AuditEvent::UPDATED, $log->event);
        $this->assertSame(['firstname' => 'Old'], $log->old_values);
        $this->assertSame(['firstname' => 'New'], $log->new_values);
        $this->assertNotNull($log->created_at);
    }

    public function test_save_log_allows_null_old_and_new_values(): void
    {
        $this->actingAs(User::factory()->create());
        $player = Player::factory()->create();

        $player->saveLog(AuditEvent::CREATED, null, ['firstname' => 'John']);
        $player->saveLog(AuditEvent::DELETED, ['firstname' => 'John'], null);

        $created = $player->auditLogs()->where('event', AuditEvent::CREATED)->sole();
        $deleted = $player->auditLogs()->where('event', AuditEvent::DELETED)->sole();
        $this->assertNull($created->old_values);
        $this->assertNull($deleted->new_values);
    }

    public function test_save_log_without_authenticated_user_stores_null_user(): void
    {
        $player = Player::factory()->create();

        $player->saveLog(AuditEvent::CREATED, null, $player->createSnapshot());

        $this->assertNull(AuditLog::sole()->user_id);
    }

    public function test_audit_logs_only_returns_the_logs_of_the_model(): void
    {
        $player = Player::factory()->create();
        $other = Player::factory()->create();
        $evaluation = GameEvaluation::factory()->create(['id' => $player->id]);

        $player->saveLog(AuditEvent::CREATED, null, []);
        $other->saveLog(AuditEvent::CREATED, null, []);
        $evaluation->saveLog(AuditEvent::CREATED, null, []);

        $this->assertSame(1, $player->auditLogs()->count());
        $this->assertSame(1, $evaluation->auditLogs()->count());
    }

    // AuditLog model
    public function test_audit_log_resolves_its_auditable_model_and_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $player = Player::factory()->create();

        $player->saveLog(AuditEvent::CREATED, null, []);

        $log = AuditLog::sole();
        $this->assertTrue($log->auditable->is($player));
        $this->assertTrue($log->user->is($user));
    }

    public function test_audit_log_keeps_its_soft_deleted_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $player = Player::factory()->create();
        $player->saveLog(AuditEvent::CREATED, null, []);

        $user->delete();

        $this->assertTrue(AuditLog::sole()->user->is($user));
    }

    public function test_audit_log_has_no_updated_at(): void
    {
        $player = Player::factory()->create();
        $player->saveLog(AuditEvent::CREATED, null, []);

        $log = AuditLog::sole();

        $this->assertNull($log->getUpdatedAtColumn());
        $this->assertArrayNotHasKey('updated_at', $log->getAttributes());
    }
}
