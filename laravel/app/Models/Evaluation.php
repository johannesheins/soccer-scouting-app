<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function categoryScores(): HasMany
    {
        return $this->hasMany(EvaluationCriteriaScore::class);
    }

    protected function casts(): array
    {
        return [
            'kickoff' => 'datetime',
        ];
    }
}
