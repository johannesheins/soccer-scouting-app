<?php

use App\Enums\Permission\ClubPermissions;
use App\Enums\Permission\EvaluationPermissions;
use App\Enums\Permission\GameEvaluationPermissions;
use App\Enums\Permission\PlayerEvaluationPermissions;
use App\Enums\Permission\PlayerPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * German group names written by the former RightSeeder, renamed to their English keys.
     */
    private array $legacyGroupNames = [
        'Spieler' => 'Players',
        'Bewertung' => 'Evaluations',
        'Spielbewertung' => 'Match evaluations',
        'Interne Spielerbewertung' => 'Internal player evaluations',
        'Verein' => 'Clubs',
    ];

    /**
     * Replaces the RightSeeder. Existing rows are updated in place,
     * so the user group assignments are kept.
     * Names and descriptions are English keys translated in the frontend via t() (lang/de.json).
     */
    public function up(): void
    {
        // Index rights of the removed index pages (Player, Evaluation, Club)
        DB::table('rights')->whereIn('id', [1, 7, 17])->delete();

        foreach ($this->legacyGroupNames as $legacyName => $name) {
            DB::table('right_groups')->where('name', $legacyName)->update(['name' => $name]);
        }

        foreach ($this->rightGroups() as $groupName => $rights) {
            DB::table('right_groups')->updateOrInsert(['name' => $groupName]);
            $groupId = DB::table('right_groups')->where('name', $groupName)->value('id');

            DB::table('rights')->upsert(
                array_map(fn (array $right) => $right + ['right_group_id' => $groupId], $rights),
                ['id'],
                ['name', 'description', 'right_group_id']
            );
        }
    }

    public function down(): void
    {
        $rights = array_merge(...array_values($this->rightGroups()));

        DB::table('rights')->whereIn('id', array_column($rights, 'id'))->delete();
        DB::table('right_groups')->whereIn('name', array_keys($this->rightGroups()))->delete();
    }

    private function rightGroups(): array
    {
        return [
            'Players' => [
                ['id' => PlayerPermissions::Search->value, 'name' => 'Player search', 'description' => 'The user may search for players'],
                ['id' => PlayerPermissions::Create->value, 'name' => 'Create player', 'description' => 'The user may create players'],
                ['id' => PlayerPermissions::View->value, 'name' => 'View player', 'description' => 'The user may view players'],
                ['id' => PlayerPermissions::Edit->value, 'name' => 'Edit player', 'description' => 'The user may edit players'],
                ['id' => PlayerPermissions::Destroy->value, 'name' => 'Delete player', 'description' => 'The user may delete players'],
                ['id' => PlayerPermissions::ViewHistory->value, 'name' => 'View player history', 'description' => 'The user may view the change history of players'],
            ],
            'Evaluations' => [
                ['id' => EvaluationPermissions::Search->value, 'name' => 'Evaluation search', 'description' => 'The user may search for evaluations'],
            ],
            'Match evaluations' => [
                ['id' => GameEvaluationPermissions::Create->value, 'name' => 'Create match evaluation', 'description' => 'The user may create match evaluations'],
                ['id' => GameEvaluationPermissions::View->value, 'name' => 'View match evaluation', 'description' => 'The user may view match evaluations they created'],
                ['id' => GameEvaluationPermissions::ViewAll->value, 'name' => 'View all match evaluations', 'description' => 'The user may view all match evaluations'],
                ['id' => GameEvaluationPermissions::Edit->value, 'name' => 'Edit match evaluation', 'description' => 'The user may edit match evaluations they created'],
                ['id' => GameEvaluationPermissions::EditAll->value, 'name' => 'Edit all match evaluations', 'description' => 'The user may edit all match evaluations'],
                ['id' => GameEvaluationPermissions::Destroy->value, 'name' => 'Delete match evaluation', 'description' => 'The user may delete match evaluations they created'],
                ['id' => GameEvaluationPermissions::DestroyAll->value, 'name' => 'Delete all match evaluations', 'description' => 'The user may delete all match evaluations'],
                ['id' => GameEvaluationPermissions::ViewCreator->value, 'name' => 'View match evaluation author', 'description' => 'The user may see the author of a match evaluation'],
                ['id' => GameEvaluationPermissions::ViewHistory->value, 'name' => 'View match evaluation history', 'description' => 'The user may view the change history of match evaluations'],
            ],
            'Internal player evaluations' => [
                ['id' => PlayerEvaluationPermissions::Create->value, 'name' => 'Create internal player evaluation', 'description' => 'The user may create internal player evaluations'],
                ['id' => PlayerEvaluationPermissions::View->value, 'name' => 'View internal player evaluation', 'description' => 'The user may view internal player evaluations they created'],
                ['id' => PlayerEvaluationPermissions::ViewAll->value, 'name' => 'View all internal player evaluations', 'description' => 'The user may view all internal player evaluations'],
                ['id' => PlayerEvaluationPermissions::Edit->value, 'name' => 'Edit internal player evaluation', 'description' => 'The user may edit internal player evaluations they created'],
                ['id' => PlayerEvaluationPermissions::EditAll->value, 'name' => 'Edit all internal player evaluations', 'description' => 'The user may edit all internal player evaluations'],
                ['id' => PlayerEvaluationPermissions::Destroy->value, 'name' => 'Delete internal player evaluation', 'description' => 'The user may delete internal player evaluations they created'],
                ['id' => PlayerEvaluationPermissions::DestroyAll->value, 'name' => 'Delete all internal player evaluations', 'description' => 'The user may delete all internal player evaluations'],
                ['id' => PlayerEvaluationPermissions::ViewCreator->value, 'name' => 'View internal player evaluation author', 'description' => 'The user may see the author of an internal player evaluation'],
                ['id' => PlayerEvaluationPermissions::ViewHistory->value, 'name' => 'View internal player evaluation history', 'description' => 'The user may view the change history of internal player evaluations'],
            ],
            'Clubs' => [
                ['id' => ClubPermissions::Search->value, 'name' => 'Club search', 'description' => 'The user may search for clubs'],
                ['id' => ClubPermissions::Create->value, 'name' => 'Create club', 'description' => 'The user may create clubs'],
                ['id' => ClubPermissions::View->value, 'name' => 'View club', 'description' => 'The user may view clubs'],
                ['id' => ClubPermissions::Edit->value, 'name' => 'Edit club', 'description' => 'The user may edit clubs'],
                ['id' => ClubPermissions::Destroy->value, 'name' => 'Delete club', 'description' => 'The user may delete clubs'],
                ['id' => ClubPermissions::ViewHistory->value, 'name' => 'View club history', 'description' => 'The user may view the change history of clubs'],
            ],
        ];
    }
};
