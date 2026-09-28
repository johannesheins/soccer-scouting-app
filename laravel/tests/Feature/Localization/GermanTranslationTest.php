<?php

namespace Tests\Feature\Localization;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\Rules\Password;
use Tests\TestCase;

class GermanTranslationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()->setLocale('de');
        app('translator')->setFallback('de');
    }

    public function test_password_update_shows_german_validation_messages()
    {
        Password::defaults(fn () => Password::min(12)->mixedCase()->numbers()->symbols());

        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('security.edit'))
            ->put(route('user-password.update'), [
                'current_password' => 'password',
                'password' => 'short',
                'password_confirmation' => 'short',
            ])
            ->assertSessionHasErrors('password');

        $messages = session('errors')->get('password');

        $this->assertContains('Passwort muss mindestens 12 Zeichen lang sein.', $messages);

        foreach ($messages as $message) {
            $this->assertStringNotContainsString('validation.', $message);
        }
    }

    public function test_wrong_current_password_shows_german_message()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('security.edit'))
            ->put(route('user-password.update'), [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertSessionHasErrors(['current_password' => 'Das Passwort ist falsch.']);
    }

    public function test_app_strings_are_translated_to_german()
    {
        $this->assertSame('Passwort aktualisiert.', __('Password updated.'));
        $this->assertSame('Profil aktualisiert.', __('Profile updated.'));
        $this->assertSame('Angeheftete Vereine aktualisiert.', __('Pinned clubs updated.'));
    }
}
