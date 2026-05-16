<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'minimum_player_age', 'multiplier'])]
class EvaluationCriteria extends Model
{
    use HasFactory;

    public $table = 'evaluation_criteria';
    public $timestamps = false;
}
