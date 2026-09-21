<?php

namespace App\Models;

use App\Interfaces\EvaluationInterface;
use App\Policies\PlayerEvaluationPolicy;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UsePolicy(PlayerEvaluationPolicy::class)]
#[Fillable('date', 'created_by', 'strengths', 'weaknesses', 'recommendation_id', 'comment', 'player_id')]
class PlayerEvaluation extends Evaluation implements EvaluationInterface
{
    public function criteriaScores(): HasMany
    {
        return $this->hasMany(EvaluationCriteriaScore::class, 'player_evaluation_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
