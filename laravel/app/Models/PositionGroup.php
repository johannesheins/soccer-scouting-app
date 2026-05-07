<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name'])]
class PositionGroup extends Model{
    use HasFactory;
    public $timestamps = false;

    public function positions(): BelongsToMany{
        return $this->belongsToMany(Position::class);
    }
}
