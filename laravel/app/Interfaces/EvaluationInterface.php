<?php

namespace App\Interfaces;

use App\Models\Evaluation;
use App\Models\EvaluationCriteriaScore;
use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface EvaluationInterface
{
    public function creator(): BelongsTo;
    public function criteriaScores(): HasMany;
    public function recommendation(): BelongsTo;
    public function loadForEvaluationView(): Evaluation;
}
