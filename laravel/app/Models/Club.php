<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['clubname', 'zip_code', 'city'])]
class Club extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }
}
