<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('game_evaluations', function (Blueprint $table){
            $table->string('home_team', 7)->nullable()->after('home_club_id');
            $table->string('guest_team', 7)->nullable()->after('guest_club_id');
        });
    }

    public function down(): void
    {
        Schema::table('game_evaluations', function (Blueprint $table){
            $table->dropColumn('home_team');
            $table->dropColumn('guest_team');
        });
    }
};
