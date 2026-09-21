<?php

namespace App\Models;

use App\Enums\EvaluationTypes;
use App\Interfaces\EvaluationInterface;
use App\Policies\GameEvaluationPolicy;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UsePolicy(GameEvaluationPolicy::class)]
#[Fillable('date', 'created_by', 'strengths', 'weaknesses', 'recommendation_id', 'comment', 'player_id', 'home_team_id', 'guest_team_id')]
class GameEvaluation extends Evaluation
{
    protected ?EvaluationTypes $evaluationType = EvaluationTypes::GAME;

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

    public function loadForView(): Evaluation
    {
        return parent::loadForView()->load('player', 'homeTeam', 'guestTeam');
    }
}
