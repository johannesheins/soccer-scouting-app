<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Player extends Model{
    use HasFactory;

    public $timestamps = false;

    public function club(): BelongsTo{
        return $this->belongsTo(Club::class);
    }

    public function positions(): HasMany{
        return $this->hasMany(Position::class);
    }
}
