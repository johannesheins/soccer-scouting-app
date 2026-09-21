<?php

namespace App\Models;

use App\Enums\EvaluationTypes;
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
    protected ?EvaluationTypes $evaluationType = EvaluationTypes::PLAYER;

    public function criteriaScores(): HasMany
    {
        return $this->hasMany(EvaluationCriteriaScore::class, 'player_evaluation_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function loadForView(): Evaluation
    {
        return parent::loadForView()->load('player');
    }
}
