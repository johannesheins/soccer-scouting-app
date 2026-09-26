<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MustRenewPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-01-01 12:00:00');
    }

    public function test_password_expires_at_is_cast_to_carbon()
    {
        $user = User::factory()->create(['password_expires_at' => '2026-03-15 08:30:00']);

        $this->assertInstanceOf(CarbonInterface::class, $user->refresh()->password_expires_at);
        $this->assertEquals('2026-03-15 08:30:00', $user->password_expires_at->format('Y-m-d H:i:s'));
    }

    public function test_password_is_valid_without_expiration()
    {
        $user = User::factory()->create(['password_expires_at' => null]);

        $this->assertTrue($user->refresh()->hasValidPassword());
    }

    public function test_password_is_valid_when_expiration_is_in_the_future()
    {
        $user = User::factory()->create(['password_expires_at' => '2026-01-01 12:00:01']);

        $this->assertTrue($user->refresh()->hasValidPassword());
    }

    public function test_password_is_invalid_when_expiration_is_in_the_past()
    {
        $user = User::factory()->create(['password_expires_at' => '2026-01-01 11:59:59']);

        $this->assertFalse($user->refresh()->hasValidPassword());
    }

    public function test_password_is_invalid_when_expiration_is_now()
    {
        $user = User::factory()->create(['password_expires_at' => '2026-01-01 12:00:00']);

        $this->assertFalse($user->refresh()->hasValidPassword());
    }

    public function test_mark_password_as_expired()
    {
        $user = User::factory()->create(['password_expires_at' => null]);

        $this->assertTrue($user->markPasswordAsExpired());

        $user->refresh();

        $this->assertEquals('2026-01-01 12:00:00', $user->password_expires_at->format('Y-m-d H:i:s'));
        $this->assertFalse($user->hasValidPassword());
    }

    public function test_clear_password_expiration()
    {
        $user = User::factory()->create(['password_expires_at' => '2025-12-31 12:00:00']);

        $this->assertTrue($user->clearPasswordExpiration());

        $user->refresh();

        $this->assertNull($user->password_expires_at);
        $this->assertTrue($user->hasValidPassword());
    }

    public function test_renew_password_sets_password_and_renews_expiration()
    {
        config(['auth.password_expires_after' => 30]);

        $user = User::factory()->create(['password_expires_at' => '2025-12-31 12:00:00']);

        $this->assertTrue($user->renewPassword('new-password'));

        $user->refresh();

        $this->assertTrue(Hash::check('new-password', $user->password));
        $this->assertEquals('2026-01-31 12:00:00', $user->password_expires_at->format('Y-m-d H:i:s'));
    }

    public function test_renew_password_expiration_uses_configured_lifetime()
    {
        config(['auth.password_expires_after' => 30]);

        $user = User::factory()->create(['password_expires_at' => '2025-12-31 12:00:00']);

        $this->assertTrue($user->renewPasswordExpiration());

        $user->refresh();

        $this->assertEquals('2026-01-31 12:00:00', $user->password_expires_at->format('Y-m-d H:i:s'));
        $this->assertTrue($user->hasValidPassword());
    }
}
