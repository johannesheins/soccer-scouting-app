<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable('date', 'created_by', 'strengths', 'weaknesses', 'recommendation_id', 'comment')]
class Evaluation extends Model
{
    use HasFactory;

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function criteriaScores(): HasMany
    {
        return $this->hasMany(EvaluationCriteriaScore::class, 'evaluation_id');
    }

    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(Recommendation::class);
    }

    public function loadForEvaluationView(): Evaluation
    {
        return $this->load('player', 'homeTeam', 'awayTeam', 'criteriaScores', 'recommendation', 'creator');
    }

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
