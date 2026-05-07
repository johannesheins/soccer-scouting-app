<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Position extends Model{
    use HasFactory;
    public $timestamps = false;

    public function group(): BelongsTo{
        return $this->belongsTo(PositionGroup::class);
    }

    public function players(): BelongsToMany{
        return $this->belongsToMany(Player::class, 'player_positions');
    }
}
