<?php

use App\Enums\RecommendationEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table){
            $table->string('strengths', 255)->nullable()->after('kickoff');
            $table->string('weaknesses', 255)->nullable()->after('strengths');
            $table->text('remarks')->nullable()->after('weaknesses');
            $table->foreignId('recommendation_id')->nullable()->after('weaknesses')->constrained()->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table){
            $table->dropForeign('evaluations_recommendation_id_foreign');
            $table->dropColumn('recommendation_id');
            $table->dropColumn('strengths');
            $table->dropColumn('weaknesses');
            $table->dropColumn('remarks');
        });
    }
};
