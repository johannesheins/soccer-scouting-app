<?php

namespace App\Concerns;

use Carbon\CarbonInterface;

/**
 * @property CarbonInterface|null $password_expires_at
 */
trait MustRenewPassword
{
    public function initializeMustRenewPassword(): void
    {
        $this->mergeCasts(['password_expires_at' => 'datetime']);
    }

    public function hasValidPassword(): bool
    {
        return $this->password_expires_at === null || $this->password_expires_at->isFuture();
    }

    public function markPasswordAsExpired(): bool
    {
        return $this->forceFill([
            'password_expires_at' => now(),
        ])->save();
    }

    public function clearPasswordExpiration(): bool
    {
        return $this->forceFill([
            'password_expires_at' => null,
        ])->save();
    }

    public function renewPasswordExpiration(): bool
    {
        return $this->forceFill([
            'password_expires_at' => now()->addDays(config('auth.password_expires_after')),
        ])->save();
    }

    public function renewPassword(string $password): bool
    {
        $this->forceFill([
            'password' => $password,
        ]);

        return $this->renewPasswordExpiration();
    }
}
