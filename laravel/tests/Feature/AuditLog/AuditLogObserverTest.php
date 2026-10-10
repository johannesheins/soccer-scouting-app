<?php

namespace Tests\Feature\AuditLog;

use App\Enums\AuditLogEventEnum;
use App\Models\AuditLog;
use App\Models\Club;
use App\Models\Player;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_created_logs_new_values(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $player = Player::factory()->create(['firstname' => 'Max']);

        $log = $player->auditLogs()->sole();
        $this->assertSame(AuditLogEventEnum::CREATED, $log->event);
        $this->assertSame('player', $log->auditable_type);
        $this->assertSame($player->id, $log->auditable_id);
        $this->assertSame($user->id, $log->user_id);
        $this->assertNull($log->old_values);
        $this->assertSame('Max', $log->new_values['firstname']);
        $this->assertNotNull($log->created_at);
    }

    public function test_updated_logs_only_changed_values(): void
    {
        $oldClub = Club::factory()->create();
        $newClub = Club::factory()->create();
        $player = Player::factory()->create(['club_id' => $oldClub->id, 'firstname' => 'Max']);

        $player->update(['club_id' => $newClub->id, 'firstname' => 'Max']);

        $log = $player->auditLogs()->where('event', AuditLogEventEnum::UPDATED)->sole();
        $this->assertEquals(['club_id' => $oldClub->id], $log->old_values);
        $this->assertEquals(['club_id' => $newClub->id], $log->new_values);
    }

    public function test_save_without_changes_does_not_log(): void
    {
        $player = Player::factory()->create();

        $player->save();

        $this->assertSame(0, $player->auditLogs()->where('event', AuditLogEventEnum::UPDATED)->count());
    }

    public function test_deleted_logs_old_values(): void
    {
        $player = Player::factory()->create(['firstname' => 'Max']);

        $player->delete();

        $log = AuditLog::query()
            ->where('auditable_id', $player->id)
            ->where('event', AuditLogEventEnum::DELETED)
            ->sole();
        $this->assertSame('Max', $log->old_values['firstname']);
        $this->assertNull($log->new_values);
    }

    public function test_user_id_is_null_without_authenticated_user(): void
    {
        $player = Player::factory()->create();

        $this->assertNull($player->auditLogs()->sole()->user_id);
    }

    public function test_user_relation_includes_soft_deleted_users(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $player = Player::factory()->create();

        $user->delete();

        $this->assertTrue($user->is($player->auditLogs()->sole()->user));
    }

    public function test_hidden_attributes_are_not_logged(): void
    {
        $player = Player::factory()->make(['firstname' => 'Max', 'height' => 180]);
        $player->setHidden(['height']);
        $player->save();

        $player->update(['firstname' => 'Moritz', 'height' => 190]);
        $player->delete();

        $logs = $player->auditLogs()->get()->keyBy(fn (AuditLog $log) => $log->event->value);
        $this->assertArrayNotHasKey('height', $logs['created']->new_values);
        $this->assertSame(['firstname' => 'Max'], $logs['updated']->old_values);
        $this->assertSame(['firstname' => 'Moritz'], $logs['updated']->new_values);
        $this->assertArrayNotHasKey('height', $logs['deleted']->old_values);
    }

    public function test_sync_audited_logs_old_and_new_ids(): void
    {
        $positions = Position::factory(4)->create();
        $player = Player::factory()->create();
        $player->positions()->attach([$positions[1]->id, $positions[0]->id]);

        $player->syncAudited('positions', [(string) $positions[3]->id, (string) $positions[1]->id]);

        $log = $player->auditLogs()->where('event', AuditLogEventEnum::UPDATED)->sole();
        $this->assertSame(['positions' => [$positions[0]->id, $positions[1]->id]], $log->old_values);
        $this->assertSame(['positions' => [$positions[1]->id, $positions[3]->id]], $log->new_values);
    }

    public function test_sync_audited_without_changes_does_not_log(): void
    {
        $positions = Position::factory(2)->create();
        $player = Player::factory()->create();
        $player->positions()->attach($positions->pluck('id'));

        $player->syncAudited('positions', $positions->pluck('id')->reverse()->all());

        $this->assertSame(0, $player->auditLogs()->where('event', AuditLogEventEnum::UPDATED)->count());
    }
}
