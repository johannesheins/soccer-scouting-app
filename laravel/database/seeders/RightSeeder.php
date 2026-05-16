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
                $this->createRight(RightEnum::PlayerIndex, 'Spieler Übersicht', 'Darf die Spieler-Übersicht sehen'),
                $this->createRight(RightEnum::PlayerSearch, 'Spieler suchen', 'Darf Spieler suchen'),
                $this->createRight(RightEnum::PlayerCreate, 'Spieler erstellen', 'Darf Spieler erstellen'),
                $this->createRight(RightEnum::PlayerView, 'Spieler ansehen', 'Darf Spieler ansehen'),
                $this->createRight(RightEnum::PlayerEdit, 'Spieler bearbeiten', 'Darf Spieler bearbeiten'),
                $this->createRight(RightEnum::PlayerDestroy, 'Spieler löschen', 'Darf Spieler löschen'),
            ],
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
