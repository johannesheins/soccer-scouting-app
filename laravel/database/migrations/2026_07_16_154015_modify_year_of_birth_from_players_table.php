<?php

use App\Models\Player;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        foreach(Player::all() as $player){
            $player->update([
                'year_of_birth' => substr($player->year_of_birth, 0, 4),
            ]);
        }
        Schema::table('players', function (Blueprint $table){
            $table->unsignedSmallInteger('year_of_birth')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table){
            $table->string('year_of_birth', 9)->comment('format 0000/0000')->change();
        });
        foreach(Player::all() as $player){
            $secondYear = $player->year_of_birth + 1;
            $player->update([
                'year_of_birth' => "{$player->year_of_birth}/{$secondYear}",
            ]);
        }
    }
};
