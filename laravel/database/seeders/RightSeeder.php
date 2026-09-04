<?php

namespace Database\Seeders;

use App\Enums\Permission\ClubPermissions;
use App\Enums\Permission\EvaluationPermissions;
use App\Enums\Permission\GameEvaluationPermissions;
use App\Enums\Permission\PlayerPermissions;
use App\Interfaces\PermissionsInterface;
use App\Models\Right;
use App\Models\RightGroup;
use Illuminate\Database\Seeder;

class RightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rightGroups = [
            'Spieler' => [
                $this->createRight(PlayerPermissions::Index, 'Spielerübersicht', 'Der Benutzer darf die Übersicht für die Spieler sehen'),
                $this->createRight(PlayerPermissions::Search, 'Spieler suchen', 'Der Benutzer darf nach Spielern suchen'),
                $this->createRight(PlayerPermissions::Create, 'Spieler erstellen', 'Der Benutzer darf Spieler erstellen'),
                $this->createRight(PlayerPermissions::View, 'Spieler ansehen', 'Der Benutzer darf Spieler ansehen'),
                $this->createRight(PlayerPermissions::Edit, 'Spieler bearbeiten', 'Der Benutzer darf Spieler bearbeiten'),
                $this->createRight(PlayerPermissions::Destroy, 'Spieler löschen', 'Der Benutzer darf Spieler löschen'),
            ],
            'Bewertung' => [
                $this->createRight(EvaluationPermissions::Index, 'Bewertungsübersicht', 'Der Benutzer darf die Übersicht für die Bewertung sehen'),
                $this->createRight(EvaluationPermissions::Search, 'Bewertung suchen', 'Der Benutzer darf nach Bewertungen suchen'),
            ],
            'Spielbewertung' => [
                $this->createRight(GameEvaluationPermissions::Create, 'Spielbewertung erstellen', 'Der Benutzer darf Spielbewertung erstellen'),
                $this->createRight(GameEvaluationPermissions::View, 'Spielbewertung ansehen', 'Der Benutzer darf die von ihm erstellten Spielbewertung ansehen'),
                $this->createRight(GameEvaluationPermissions::ViewAll, 'Alle Spielbewertung ansehen', 'Der Benutzer darf alle Spielbewertung ansehen'),
                $this->createRight(GameEvaluationPermissions::Edit, 'Spielbewertung bearbeiten', 'Der Benutzer darf die von ihm erstellten Spielbewertung bearbeiten'),
                $this->createRight(GameEvaluationPermissions::EditAll, 'Alle Spielbewertung bearbeiten', 'Der Benutzer darf alle Spielbewertung bearbeiten'),
                $this->createRight(GameEvaluationPermissions::Destroy, 'Spielbewertung löschen', 'Der Benutzer darf die von ihm erstellten Spielbewertung löschen'),
                $this->createRight(GameEvaluationPermissions::DestroyAll, 'Alle Spielbewertung löschen', 'Der Benutzer darf alle Spielbewertung löschen'),
                $this->createRight(GameEvaluationPermissions::ViewCreator, 'Autor sehen ', 'Der Benutzer darf den Autor sehen'),
            ],
            'Verein' => [
                $this->createRight(ClubPermissions::Index, 'Verein Übersicht', 'Der Benutzer darf die Übersicht für die Vereine sehen'),
                $this->createRight(ClubPermissions::Search, 'Verein suchen', 'Der Benutzer darf nach Vereinen suchen'),
                $this->createRight(ClubPermissions::Create, 'Verein erstellen', 'Der Benutzer darf Vereine erstellen'),
                $this->createRight(ClubPermissions::View, 'Verein ansehen', 'Der Benutzer darf Vereine ansehen'),
                $this->createRight(ClubPermissions::Edit, 'Verein bearbeiten', 'Der Benutzer darf Vereine bearbeiten'),
                $this->createRight(ClubPermissions::Destroy, 'Verein löschen', 'Der Benutzer darf Vereine löschen'),
            ],
        ];

        Right::unguarded(function () use ($rightGroups) {
            foreach ($rightGroups as $groupName => $rights) {
                $group = RightGroup::updateOrCreate(['name' => $groupName]);
                foreach ($rights as $right) {
                    $group->rights()->updateOrCreate(['id' => $right['id']], $right);
                }
            }
        });
    }

    private function createRight(PermissionsInterface $id, string $name, string $description): array
    {
        return [
            'id' => $id->value,
            'name' => $name,
            'description' => $description,
        ];
    }
}
