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
     * Replaces the RightSeeder. Existing rows are updated in place,
     * so the user group assignments are kept.
     */
    public function up(): void
    {
        // Index rights of the removed index pages (Player, Evaluation, Club)
        DB::table('rights')->whereIn('id', [1, 7, 17])->delete();

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
            'Spieler' => [
                ['id' => PlayerPermissions::Search->value, 'name' => 'Spieler suchen', 'description' => 'Der Benutzer darf nach Spielern suchen'],
                ['id' => PlayerPermissions::Create->value, 'name' => 'Spieler erstellen', 'description' => 'Der Benutzer darf Spieler erstellen'],
                ['id' => PlayerPermissions::View->value, 'name' => 'Spieler ansehen', 'description' => 'Der Benutzer darf Spieler ansehen'],
                ['id' => PlayerPermissions::Edit->value, 'name' => 'Spieler bearbeiten', 'description' => 'Der Benutzer darf Spieler bearbeiten'],
                ['id' => PlayerPermissions::Destroy->value, 'name' => 'Spieler löschen', 'description' => 'Der Benutzer darf Spieler löschen'],
                ['id' => PlayerPermissions::ViewHistory->value, 'name' => 'Änderungen der Spieler ansehen', 'description' => 'Der Benutzer darf den Änderungsverlauf von Spielern ansehen'],
            ],
            'Bewertung' => [
                ['id' => EvaluationPermissions::Search->value, 'name' => 'Bewertung suchen', 'description' => 'Der Benutzer darf nach Bewertungen suchen'],
            ],
            'Spielbewertung' => [
                ['id' => GameEvaluationPermissions::Create->value, 'name' => 'Spielbewertung erstellen', 'description' => 'Der Benutzer darf Spielbewertung erstellen'],
                ['id' => GameEvaluationPermissions::View->value, 'name' => 'Spielbewertung ansehen', 'description' => 'Der Benutzer darf die von ihm erstellten Spielbewertung ansehen'],
                ['id' => GameEvaluationPermissions::ViewAll->value, 'name' => 'Alle Spielbewertung ansehen', 'description' => 'Der Benutzer darf alle Spielbewertung ansehen'],
                ['id' => GameEvaluationPermissions::Edit->value, 'name' => 'Spielbewertung bearbeiten', 'description' => 'Der Benutzer darf die von ihm erstellten Spielbewertung bearbeiten'],
                ['id' => GameEvaluationPermissions::EditAll->value, 'name' => 'Alle Spielbewertung bearbeiten', 'description' => 'Der Benutzer darf alle Spielbewertung bearbeiten'],
                ['id' => GameEvaluationPermissions::Destroy->value, 'name' => 'Spielbewertung löschen', 'description' => 'Der Benutzer darf die von ihm erstellten Spielbewertung löschen'],
                ['id' => GameEvaluationPermissions::DestroyAll->value, 'name' => 'Alle Spielbewertung löschen', 'description' => 'Der Benutzer darf alle Spielbewertung löschen'],
                ['id' => GameEvaluationPermissions::ViewCreator->value, 'name' => 'Autor der Spielbewertung sehen', 'description' => 'Der Benutzer darf den Autor sehen'],
                ['id' => GameEvaluationPermissions::ViewHistory->value, 'name' => 'Änderungen der Spielbewertung ansehen', 'description' => 'Der Benutzer darf den Änderungsverlauf von Spielbewertungen ansehen'],
            ],
            'Interne Spielerbewertung' => [
                ['id' => PlayerEvaluationPermissions::Create->value, 'name' => 'Interne Spielerbewertung erstellen', 'description' => 'Der Benutzer darf interne Spielerbewertungen erstellen'],
                ['id' => PlayerEvaluationPermissions::View->value, 'name' => 'Interne Spielerbewertung ansehen', 'description' => 'Der Benutzer darf die von ihm erstellten internen Spielerbewertungen ansehen'],
                ['id' => PlayerEvaluationPermissions::ViewAll->value, 'name' => 'Alle internen Spielerbewertungen ansehen', 'description' => 'Der Benutzer darf alle internen Spielerbewertungen ansehen'],
                ['id' => PlayerEvaluationPermissions::Edit->value, 'name' => 'Interne Spielerbewertung bearbeiten', 'description' => 'Der Benutzer darf die von ihm erstellten internen Spielerbewertungen bearbeiten'],
                ['id' => PlayerEvaluationPermissions::EditAll->value, 'name' => 'Alle internen Spielerbewertungen bearbeiten', 'description' => 'Der Benutzer darf alle internen Spielerbewertungen bearbeiten'],
                ['id' => PlayerEvaluationPermissions::Destroy->value, 'name' => 'Interne Spielerbewertung löschen', 'description' => 'Der Benutzer darf die von ihm erstellten internen Spielerbewertungen löschen'],
                ['id' => PlayerEvaluationPermissions::DestroyAll->value, 'name' => 'Alle internen Spielerbewertungen löschen', 'description' => 'Der Benutzer darf alle internen Spielerbewertungen löschen'],
                ['id' => PlayerEvaluationPermissions::ViewCreator->value, 'name' => 'Autor der internen Spielerbewertung sehen', 'description' => 'Der Benutzer darf den Autor einer internen Spielerbewertung sehen'],
                ['id' => PlayerEvaluationPermissions::ViewHistory->value, 'name' => 'Änderungen der internen Spielerbewertung ansehen', 'description' => 'Der Benutzer darf den Änderungsverlauf von internen Spielerbewertungen ansehen'],
            ],
            'Verein' => [
                ['id' => ClubPermissions::Search->value, 'name' => 'Verein suchen', 'description' => 'Der Benutzer darf nach Vereinen suchen'],
                ['id' => ClubPermissions::Create->value, 'name' => 'Verein erstellen', 'description' => 'Der Benutzer darf Vereine erstellen'],
                ['id' => ClubPermissions::View->value, 'name' => 'Verein ansehen', 'description' => 'Der Benutzer darf Vereine ansehen'],
                ['id' => ClubPermissions::Edit->value, 'name' => 'Verein bearbeiten', 'description' => 'Der Benutzer darf Vereine bearbeiten'],
                ['id' => ClubPermissions::Destroy->value, 'name' => 'Verein löschen', 'description' => 'Der Benutzer darf Vereine löschen'],
                ['id' => ClubPermissions::ViewHistory->value, 'name' => 'Änderungen der Vereine ansehen', 'description' => 'Der Benutzer darf den Änderungsverlauf von Vereinen ansehen'],
            ],
        ];
    }
};
