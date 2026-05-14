<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable('name')]
class RightGroup extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function rights(): HasMany{
        return $this->hasMany(Right::class);
    }
}
