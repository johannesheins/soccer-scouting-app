<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\RightGroup;
use App\Models\User;
use App\Models\UserGroup;
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

        User::factory()->create([
            'firstname' => 'Max',
            'lastname' => 'Mustermann',
            'email' => 'max.mustermann@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->call(PositionSeeder::class);

        Club::factory(10)->create();

        $this->call(RightSeeder::class);

        UserGroup::factory(10)->create();
    }
}
