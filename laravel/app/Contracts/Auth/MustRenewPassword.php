<?php

namespace App\Contracts\Auth;

interface MustRenewPassword
{
    /**
     * Determine if the user's password has not expired yet.
     */
    public function hasValidPassword(): bool;

    /**
     * Expire the password immediately, forcing the user to choose a new one.
     */
    public function markPasswordAsExpired(): bool;

    /**
     * Remove the expiration, so the password never expires.
     */
    public function clearPasswordExpiration(): bool;

    /**
     * Let the password expire after the configured lifetime.
     */
    public function renewPasswordExpiration(): bool;

    /**
     * Set a new password and let it expire after the configured lifetime.
     */
    public function renewPassword(string $password): bool;
}
