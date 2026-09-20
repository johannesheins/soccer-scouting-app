<?php

use App\Models\EvaluationCriteria;
use App\Models\GameEvaluation;
use App\Models\PlayerEvaluation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evaluation_criteria_scores', function (Blueprint $table){
            $table->id();
            $table->foreignIdFor(GameEvaluation::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(PlayerEvaluation::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(EvaluationCriteria::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedTinyInteger('score');
            $table->unique(['game_evaluation_id', 'evaluation_criteria_id'], 'scores_game_evaluation_criteria_unique');
            $table->unique(['player_evaluation_id', 'evaluation_criteria_id'], 'scores_player_evaluation_criteria_unique');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_criteria_scores');
    }
};
