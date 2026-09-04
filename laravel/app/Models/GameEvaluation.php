<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable('date', 'created_by', 'strengths', 'weaknesses', 'recommendation_id', 'comment', 'player_id', 'home_team_id', 'guest_team_id')]
class GameEvaluation extends Evaluation
{
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
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
