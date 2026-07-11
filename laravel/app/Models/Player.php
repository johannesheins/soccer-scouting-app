<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['firstname', 'lastname', 'year_of_birth', 'height', 'strong_foot', 'club_id'])]
class Player extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'player_positions');
    }

    public function loadForPlayerView(): Player
    {
        return $this->load('positions', 'club');
    }
}
