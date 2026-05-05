<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Position;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        Position::factory()->createMany([
            ['position_code' => 'TW'],

            ['position_code' => 'IV'],
            ['position_code' => 'AV'],
            ['position_code' => 'LAV'],
            ['position_code' => 'RAV'],

            ['position_code' => 'ZM'],
            ['position_code' => 'RM'],
            ['position_code' => 'LM'],
            ['position_code' => 'ZDM'],
            ['position_code' => 'ZOM'],

            ['position_code' => 'ST'],
            ['position_code' => 'MS'],
            ['position_code' => 'LA/LS'],
            ['position_code' => 'RA/RS'],
            ['position_code' => 'LF'],
            ['position_code' => 'RF'],
        ]);
    }
}
