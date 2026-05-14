<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable('name')]
class UserGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public $timestamps = false;

    protected $table = 'user_groups';

    public function rights(): BelongsToMany
    {
        return $this->belongsToMany(Right::class, 'user_group_rights');
    }

    public function users(): HasMany{
        return $this->hasMany(User::class, 'user_group_id', 'id');
    }
}
