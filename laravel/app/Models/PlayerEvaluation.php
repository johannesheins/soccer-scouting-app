<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable('date', 'created_by', 'strengths', 'weaknesses', 'recommendation_id', 'comment', 'player_id')]
class PlayerEvaluation extends Evaluation
{
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
