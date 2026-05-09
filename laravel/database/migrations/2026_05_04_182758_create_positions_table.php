<?php

use App\Models\PositionGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('position_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
        });

        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('position_code')->unique();
            $table->foreignIdFor(PositionGroup::class)->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('position_groups');
        Schema::dropIfExists('positions');
    }
};
