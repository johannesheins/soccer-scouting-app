<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('game_evaluations', function (Blueprint $table){
            $table->dropForeign(['home_team_id']);
            $table->dropForeign(['guest_team_id']);
        });

        Schema::table('game_evaluations', function (Blueprint $table){
            $table->renameColumn('home_team_id', 'home_club_id');
            $table->renameColumn('guest_team_id', 'guest_club_id');
        });

        Schema::table('game_evaluations', function (Blueprint $table){
            $table->foreign('home_club_id')->references('id')->on('clubs')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('guest_club_id')->references('id')->on('clubs')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('game_evaluations', function (Blueprint $table){
            $table->dropForeign(['home_club_id']);
            $table->dropForeign(['guest_club_id']);
        });

        Schema::table('game_evaluations', function (Blueprint $table){
            $table->renameColumn('home_club_id', 'home_team_id');
            $table->renameColumn('guest_club_id', 'guest_team_id');
        });

        Schema::table('game_evaluations', function (Blueprint $table){
            $table->foreign('home_team_id')->references('id')->on('clubs')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('guest_team_id')->references('id')->on('clubs')->cascadeOnUpdate()->nullOnDelete();
        });
    }
};
