<?php

namespace Tests\Feature\Seeders;

use App\Enums\Permission\GameEvaluationPermissions;
use App\Enums\Permission\PlayerEvaluationPermissions;
use App\Models\Right;
use App\Models\RightGroup;
use Database\Seeders\RightSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RightSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(RightSeeder::class);
        $count = Right::count();

        $this->seed(RightSeeder::class);

        $this->assertSame($count, Right::count());
    }

    public function test_seeder_creates_player_evaluation_rights(): void
    {
        $this->seed(RightSeeder::class);

        $group = RightGroup::where('name', 'Interne Spielerbewertung')->firstOrFail();

        foreach (PlayerEvaluationPermissions::cases() as $permission) {
            $this->assertSame($group->id, Right::findOrFail($permission->value)->right_group_id);
        }
    }

    public function test_seeder_moves_existing_right_that_belongs_to_another_group(): void
    {
        $oldGroup = RightGroup::create(['name' => 'Bewertung']);
        $newGroup = RightGroup::create(['name' => 'Spielbewertung']);

        Right::unguarded(fn () => Right::create([
            'id' => GameEvaluationPermissions::Create->value,
            'name' => 'Bewertung erstellen',
            'description' => 'Alt',
            'right_group_id' => $oldGroup->id,
        ]));

        $this->seed(RightSeeder::class);

        $right = Right::findOrFail(GameEvaluationPermissions::Create->value);
        $this->assertSame($newGroup->id, $right->right_group_id);
        $this->assertSame('Spielbewertung erstellen', $right->name);
    }
}
