<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationCriteriaScore extends Model
{
    use HasFactory;

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function evaluationCriterion(): BelongsTo
    {
        return $this->belongsTo(EvaluationCriteria::class, 'evaluation_criterion_id');
    }
}
