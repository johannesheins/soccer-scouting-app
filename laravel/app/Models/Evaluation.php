<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable('player_id', 'user_id', 'home_team_id', 'away_team_id', 'kickoff', 'kickoff_date', 'kickoff_time', 'strengths', 'weaknesses', 'recommendation_id', 'comment')]
class Evaluation extends Model
{
    use HasFactory;

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'away_team_id');
    }

    public function categoryScores(): HasMany
    {
        return $this->hasMany(EvaluationCriteriaScore::class);
    }

    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(Recommendation::class);
    }

    protected function casts(): array
    {
        return [
            'kickoff' => 'datetime',
            'kickoff_date' => 'date',
            'kickoff_time' => 'datetime:H:i:s',
        ];
    }
}
