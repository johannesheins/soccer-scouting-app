<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'minimum_player_age', 'evaluation_criteria_group_id'])]
class EvaluationCriteria extends Model
{
    use HasFactory;
    use Auditable;

    public $table = 'evaluation_criteria';
    public $timestamps = false;

    public function evaluationCriteriaScores(): HasMany
    {
        return $this->hasMany(EvaluationCriteriaScore::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(EvaluationCriteriaGroup::class, 'evaluation_criteria_group_id');
    }
}
