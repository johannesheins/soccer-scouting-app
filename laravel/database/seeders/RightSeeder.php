<?php

namespace Database\Seeders;

use App\Enums\RightEnum;
use App\Models\RightGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                $this->createRight(RightEnum::PlayerIndex, 'Spieler Übersicht', 'Der Benutzer darf die Übersicht für die Spieler sehen'),
                $this->createRight(RightEnum::PlayerSearch, 'Spieler suchen', 'Der Benutzer darf nach Spielern suchen'),
                $this->createRight(RightEnum::PlayerCreate, 'Spieler erstellen', 'Der Benutzer darf Spieler erstellen'),
                $this->createRight(RightEnum::PlayerView, 'Spieler ansehen', 'Der Benutzer darf Spieler ansehen'),
                $this->createRight(RightEnum::PlayerEdit, 'Spieler bearbeiten', 'Der Benutzer darf Spieler bearbeiten'),
                $this->createRight(RightEnum::PlayerDestroy, 'Spieler löschen', 'Der Benutzer darf Spieler löschen'),
            ],
            'Bewertung' => [
                $this->createRight(RightEnum::EvaluationIndex, 'Bewertungs Übersicht', 'Der Benutzer darf die Übersicht für die Bewertung sehen'),
                $this->createRight(RightEnum::EvaluationSearch, 'Bewertung suchen', 'Der Benutzer darf nach Bewertungen suchen'),
                $this->createRight(RightEnum::EvaluationCreate, 'Bewertung erstellen', 'Der Benutzer darf Bewertungen erstellen'),
                $this->createRight(RightEnum::EvaluationView, 'Bewertung ansehen', 'Der Benutzer darf die von ihm erstellten Bewertungen ansehen'),
                $this->createRight(RightEnum::EvaluationViewAll, 'Alle Bewertung ansehen', 'Der Benutzer darf alle Bewertungen ansehen'),
                $this->createRight(RightEnum::EvaluationEdit, 'Bewertung bearbeiten', 'Der Benutzer darf die von ihm erstellten Bewertung bearbeiten'),
                $this->createRight(RightEnum::EvaluationEditAll, 'Alle Bewertung bearbeiten', 'Der Benutzer darf alle Bewertung bearbeiten'),
                $this->createRight(RightEnum::EvaluationDestroy, 'Bewertung löschen', 'Der Benutzer darf die von ihm erstellten Bewertung löschen'),
                $this->createRight(RightEnum::EvaluationDestroyAll, 'Alle Bewertung löschen', 'Der Benutzer darf alle Bewertung löschen'),
            ]
        ];

        foreach ($rightGroups as $groupName => $rights) {
            $group = RightGroup::create([
                'name' => $groupName
            ]);
            foreach ($rights as $right) {
                $group->rights()->create($right);
            }
        }
    }

    private function createRight(RightEnum $id, string $name, string $description): array
    {
        return [
            'id' => $id,
            'name' => $name,
            'description' => $description,
        ];
    }
}
