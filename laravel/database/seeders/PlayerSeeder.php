<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\Position;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    public function run(): void
    {
        Player::factory(30)->create();

        foreach (Player::all() as $player) {
            $count = rand(1, 4);
            $positions = Position::inRandomOrder()->limit($count)->pluck('id');
            $player->positions()->attach($positions);
        }
    }
}
