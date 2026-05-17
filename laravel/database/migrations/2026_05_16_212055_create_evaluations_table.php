<?php

use App\Models\Club;
use App\Models\Player;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table){
            $table->id();
            $table->foreignIdFor(Player::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(User::class)->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Club::class, 'home_team_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Club::class, 'away_team_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->text('comment');
            $table->dateTime('kickoff');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
