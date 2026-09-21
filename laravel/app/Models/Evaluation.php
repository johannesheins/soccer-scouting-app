<?php

namespace App\Models;

use App\Enums\EvaluationTypes;
use App\Interfaces\EvaluationInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected ?EvaluationTypes $evaluationType = null;
    protected $appends = ['evaluation_type'];

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

    public function evaluationType(): Attribute
    {
        if (!$this->evaluationType instanceof EvaluationTypes) {
            throw new LogicException(static::class . ' must set $evaluationType to a case of ' . EvaluationTypes::class . '.');
        }

        return Attribute::get(fn () => $this->evaluationType->value);
    }

    public function loadForView(): Evaluation
    {
        return $this->load('criteriaScores', 'recommendation', 'creator');
    }

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
