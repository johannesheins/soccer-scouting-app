<?php

namespace App\Models;

use App\Policies\EvaluationPolicy;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UsePolicy(EvaluationPolicy::class)]
#[Fillable('date', 'created_by', 'strengths', 'weaknesses', 'recommendation_id', 'comment', 'player_id', 'home_team_id', 'guest_team_id')]
class GameEvaluation extends Evaluation
{
    public function criteriaScores(): HasMany
    {
        return $this->hasMany(EvaluationCriteriaScore::class, 'game_evaluation_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'player_id');
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'home_team_id');
    }

    public function guestTeam(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'guest_team_id');
    }
}
