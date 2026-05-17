<?php

use App\Models\Evaluation;
use App\Models\EvaluationCriteria;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evaluation_criteria_scores', function (Blueprint $table){
            $table->foreignIdFor(Evaluation::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(EvaluationCriteria::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedTinyInteger('score');
            $table->primary(['evaluation_id', 'evaluation_criteria_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_criteria_scores');
    }
};
