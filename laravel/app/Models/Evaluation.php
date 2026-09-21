<?php

namespace App\Models;

use App\Interfaces\EvaluationInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

/**
 * Abstract class
 */
#[Fillable('date', 'created_by', 'strengths', 'weaknesses', 'recommendation_id', 'comment')]
class Evaluation extends Model implements EvaluationInterface
{
    use HasFactory;

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function criteriaScores(): HasMany
    {
        throw new LogicException(static::class . ' must implement '. __FUNCTION__ .'().');
    }

    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(Recommendation::class);
    }

    public function loadForEvaluationView(): Evaluation
    {
        throw new LogicException(static::class . ' must implement '. __FUNCTION__ .'().');
    }

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
