<?php

use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('user_pinned_clubs', function (Blueprint $table) {
            $table->foreignIdFor(User::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Club::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->primary(['user_id', 'club_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_pinned_clubs');
    }
};
