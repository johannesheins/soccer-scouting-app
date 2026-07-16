<?php

use App\Enums\FootEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->string('firstname')->nullable()->change();
            $table->string('lastname')->nullable()->change();
            $table->integer('height')->comment('given in cm')->nullable()->change();
            $table->enum('strong_foot', array_column(FootEnum::cases(), 'value'))->nullable()->change();
            $table->foreignId('club_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->string('firstname')->nullable(false)->change();
            $table->string('lastname')->nullable(false)->change();
            $table->integer('height')->comment('given in cm')->nullable(false)->change();
            $table->enum('strong_foot', array_column(FootEnum::cases(), 'value'))->nullable(false)->change();
            $table->foreignId('club_id')->nullable(false)->change();
        });
    }
};
