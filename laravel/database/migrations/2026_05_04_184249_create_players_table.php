<?php

use App\Models\Club;
use App\Models\Player;
use App\Models\Position;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->unsignedSmallInteger('year_of_birth');
            $table->foreignIdFor(Club::class)->constrained()->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::create('player_positions', function (Blueprint $table) {
            $table->foreignIdFor(Player::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Position::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->primary(['player_id', 'position_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
        Schema::dropIfExists('player_positions');
    }
};
