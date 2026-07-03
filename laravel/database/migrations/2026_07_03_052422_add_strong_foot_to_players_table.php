<?php

use App\Enums\FootEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table){
            $table->enum('strong_foot', array_column(FootEnum::cases(), 'value'))->after('year_of_birth');
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table){
            $table->dropColumn('strong_foot');
        });
    }
};
