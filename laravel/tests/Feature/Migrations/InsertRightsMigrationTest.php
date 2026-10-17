<?php

namespace Tests\Feature\Migrations;

use App\Enums\Permission\ClubPermissions;
use App\Enums\Permission\EvaluationPermissions;
use App\Enums\Permission\GameEvaluationPermissions;
use App\Enums\Permission\PlayerEvaluationPermissions;
use App\Enums\Permission\PlayerPermissions;
use App\Models\Right;
use App\Models\RightGroup;
use App\Models\UserGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InsertRightsMigrationTest extends TestCase
{
    use RefreshDatabase;

    private function migration(): Migration
    {
        return require database_path('migrations/2026_10_17_120000_insert_rights.php');
    }

    public function test_every_permission_has_a_right(): void
    {
        $permissions = [
            ...PlayerPermissions::cases(),
            ...EvaluationPermissions::cases(),
            ...GameEvaluationPermissions::cases(),
            ...PlayerEvaluationPermissions::cases(),
            ...ClubPermissions::cases(),
        ];

        foreach ($permissions as $permission) {
            $this->assertNotNull(Right::find($permission->value), "Right for [" . $permission::class . "::{$permission->name}] is missing.");
        }
    }

    public function test_creates_player_evaluation_rights_in_their_group(): void
    {
        $group = RightGroup::where('name', 'Interne Spielerbewertung')->firstOrFail();

        foreach (PlayerEvaluationPermissions::cases() as $permission) {
            $this->assertSame($group->id, Right::findOrFail($permission->value)->right_group_id);
        }
    }

    public function test_migration_is_idempotent(): void
    {
        $rightCount = Right::count();
        $groupCount = RightGroup::count();

        $this->migration()->up();

        $this->assertSame($rightCount, Right::count());
        $this->assertSame($groupCount, RightGroup::count());
    }

    public function test_migration_updates_existing_right_and_keeps_user_group_assignment(): void
    {
        $oldGroup = RightGroup::where('name', 'Bewertung')->firstOrFail();
        $right = Right::findOrFail(GameEvaluationPermissions::Create->value);
        $right->forceFill(['name' => 'Bewertung erstellen', 'description' => 'Alt', 'right_group_id' => $oldGroup->id])->save();
        $userGroup = UserGroup::factory()->create();
        $userGroup->rights()->attach($right);

        $this->migration()->up();

        $right->refresh();
        $this->assertSame(RightGroup::where('name', 'Spielbewertung')->value('id'), $right->right_group_id);
        $this->assertSame('Spielbewertung erstellen', $right->name);
        $this->assertTrue($userGroup->rights()->whereKey($right->id)->exists());
    }
}
