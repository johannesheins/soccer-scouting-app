<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Player;
use App\Models\Position;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DatabaseSeeder::class);

        Player::factory()->count(10)->create();
        Position::factory()->create();

        foreach(Player::all() as $player) {
            $count = rand(1, Position::count());
            $positions = Position::inRandomOrder()->limit($count)->pluck('id');
            $player->positions()->attach($positions);
        }
    }
}
