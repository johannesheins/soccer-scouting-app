<?php

use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('player_quick_search_user_clubs', function (Blueprint $table){
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Club::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->primary(['user_id', 'club_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_quick_search_user_clubs');
    }
};
