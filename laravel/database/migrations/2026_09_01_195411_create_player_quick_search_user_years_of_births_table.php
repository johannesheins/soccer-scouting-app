<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('player_quick_search_user_years_of_births', function (Blueprint $table) {
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedSmallInteger('year_of_birth');
            $table->primary(['user_id', 'year_of_birth']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_quick_search_user_years_of_births');
    }
};
