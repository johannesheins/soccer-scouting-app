<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table){
            $table->string('year_of_birth', 9)->comment('format 0000/0000')->change();
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table){
            $table->unsignedSmallInteger('year_of_birth')->change();
        });
    }
};
