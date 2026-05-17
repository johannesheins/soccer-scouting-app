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
            $table->id();
            $table->foreignIdFor(Evaluation::class);
            $table->foreignIdFor(EvaluationCriteria::class, 'evaluation_criterion_id');
            $table->unsignedTinyInteger('score');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_criteria_scores');
    }
};
