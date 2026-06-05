<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evaluation_criteria', function (Blueprint $table){
            $table->id();
            $table->string('name')->unique();
            $table->unsignedSmallInteger('minimum_player_age')->nullable();
            $table->unsignedTinyInteger('multiplier')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_criteria');
    }
};
