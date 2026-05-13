<?php

use App\Models\Right;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('user_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('user_group_rights', function (Blueprint $table) {
            $table->foreignIdFor(UserGroup::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Right::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->primary(['user_group_id', 'right_id']);
        });

        Schema::create('user_group_members', function (Blueprint $table) {
            $table->foreignIdFor(UserGroup::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->primary(['user_group_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_groups');
        Schema::dropIfExists('user_group_rights');
        Schema::dropIfExists('user_group_members');
    }
};
