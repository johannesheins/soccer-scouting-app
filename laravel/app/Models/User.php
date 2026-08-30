<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Enums\RightEnum as RightEnum;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

#[Fillable(['firstname', 'lastname', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use HasRelationships;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function userGroups(): BelongsToMany
    {
        return $this->belongsToMany(UserGroup::class, 'user_group_members');
    }

    public function rights(): HasManyDeep
    {
        return $this->hasManyDeep(
            Right::class,
            ['user_group_members', UserGroup::class, 'user_group_rights'],
            ['user_id', 'id', 'user_group_id', 'id'],
            ['id', 'user_group_id', 'id', 'right_id']
        );
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function pinnedClubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class, 'user_pinned_clubs');
    }

    public function hasRight(RightEnum $right): bool
    {
        if ($this->isAdministrator()) {
            return true;
        }

        return $this->rights()->where('rights.id', $right->value)->exists();
    }

    public function isAdministrator(): bool
    {
        return $this->is_administrator;
    }
}
