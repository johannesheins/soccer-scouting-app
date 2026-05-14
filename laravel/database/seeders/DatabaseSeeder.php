<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Position;
use App\Models\PositionGroup;
use App\Models\RightGroup;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->administrator()->create([
            'firstname' => 'Test',
            'lastname' => 'User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        PositionGroup::factory()->createMany([
            ['id' => 1, 'name' => 'Abwehr'],
            ['id' => 2, 'name' => 'Mittelfeld'],
            ['id' => 3, 'name' => 'Angriff'],
        ]);

        Position::factory()->createMany([
            ['position_group_id' => 1, 'position_code' => 'TW'],

            ['position_group_id' => 1, 'position_code' => 'IV'],
            ['position_group_id' => 1, 'position_code' => 'AV'],
            ['position_group_id' => 1, 'position_code' => 'LAV'],
            ['position_group_id' => 1, 'position_code' => 'RAV'],

            ['position_group_id' => 2, 'position_code' => 'ZM'],
            ['position_group_id' => 2, 'position_code' => 'RM'],
            ['position_group_id' => 2, 'position_code' => 'LM'],
            ['position_group_id' => 2, 'position_code' => 'ZDM'],
            ['position_group_id' => 2, 'position_code' => 'ZOM'],

            ['position_group_id' => 3, 'position_code' => 'ST'],
            ['position_group_id' => 3, 'position_code' => 'MS'],
            ['position_group_id' => 3, 'position_code' => 'LA/LS'],
            ['position_group_id' => 3, 'position_code' => 'RA/RS'],
            ['position_group_id' => 3, 'position_code' => 'LF'],
            ['position_group_id' => 3, 'position_code' => 'RF'],
        ]);

        Club::factory(10)->create();

        $rightGroups = [
            'Spieler' => [
                ['name' => 'player.view', 'description' => 'Spieler anzeigen'],
                ['name' => 'player.create', 'description' => 'Spieler anlegen'],
                ['name' => 'player.edit', 'description' => 'Spieler bearbeiten'],
                ['name' => 'player.delete', 'description' => 'Spieler löschen'],
            ],
            'Vereine' => [
                ['name' => 'club.view', 'description' => 'Vereine anzeigen'],
                ['name' => 'club.create', 'description' => 'Vereine anlegen'],
                ['name' => 'club.edit', 'description' => 'Vereine bearbeiten'],
                ['name' => 'club.delete', 'description' => 'Vereine löschen'],
            ],
            'Administration' => [
                ['name' => 'user.view', 'description' => 'Benutzer anzeigen'],
                ['name' => 'user.create', 'description' => 'Benutzer anlegen'],
                ['name' => 'user.edit', 'description' => 'Benutzer bearbeiten'],
                ['name' => 'user.delete', 'description' => 'Benutzer löschen'],
                ['name' => 'user-group.manage', 'description' => 'Benutzergruppen verwalten'],
            ],
        ];

        foreach ($rightGroups as $groupName => $rights) {
            $group = RightGroup::create(['name' => $groupName]);
            foreach ($rights as $right) {
                $group->rights()->create($right);
            }
        }
    }
}
