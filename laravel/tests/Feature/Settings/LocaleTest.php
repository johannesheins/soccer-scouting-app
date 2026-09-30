<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.locale' => 'en', 'app.available_locales' => ['en' => 'English', 'de' => 'Deutsch']]);
    }

    public function test_appearance_page_lists_available_locales()
    {
        $this->actingAs(User::factory()->create())
            ->get(route('appearance.edit'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('settings/appearance')
                ->where('locales', ['en' => 'English', 'de' => 'Deutsch']));
    }

    public function test_guests_are_redirected_to_the_login_page()
    {
        $this->get(route('appearance.edit'))->assertRedirect(route('login'));
    }

    public function test_default_locale_is_used_without_cookie()
    {
        $this->actingAs(User::factory()->create())
            ->get(route('appearance.edit'))
            ->assertSee('<html lang="en"', false);
    }

    public function test_locale_cookie_sets_the_application_locale()
    {
        $this->actingAs(User::factory()->create())
            ->withUnencryptedCookie('locale', 'de')
            ->get(route('appearance.edit'))
            ->assertSee('<html lang="de"', false);
    }

    public function test_unsupported_locale_cookie_is_ignored()
    {
        $this->actingAs(User::factory()->create())
            ->withUnencryptedCookie('locale', 'xx')
            ->get(route('appearance.edit'))
            ->assertSee('<html lang="en"', false);
    }

    public function test_backend_messages_use_the_locale_from_the_cookie()
    {
        $this->actingAs(User::factory()->create())
            ->withUnencryptedCookie('locale', 'de')
            ->from(route('security.edit'))
            ->put(route('user-password.update'), [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertSessionHasErrors(['current_password' => 'Das Passwort ist falsch.']);
    }

    public function test_user_can_update_their_locale()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('appearance.edit'))
            ->patch(route('locale.update'), ['locale' => 'de'])
            ->assertRedirect(route('appearance.edit'))
            ->assertPlainCookie('locale', 'de');

        $this->assertSame('de', $user->fresh()->locale);
    }

    public function test_unsupported_locale_is_rejected()
    {
        $user = User::factory()->create(['locale' => 'en']);

        $this->actingAs($user)
            ->from(route('appearance.edit'))
            ->patch(route('locale.update'), ['locale' => 'xx'])
            ->assertSessionHasErrors('locale');

        $this->assertSame('en', $user->fresh()->locale);
    }

    public function test_guests_can_update_the_locale_cookie()
    {
        $this->from(route('login'))
            ->patch(route('locale.update'), ['locale' => 'de'])
            ->assertRedirect(route('login'))
            ->assertPlainCookie('locale', 'de');
    }

    public function test_guests_cannot_set_an_unsupported_locale()
    {
        $this->from(route('login'))
            ->patch(route('locale.update'), ['locale' => 'xx'])
            ->assertSessionHasErrors('locale')
            ->assertCookieMissing('locale');
    }

    public function test_available_locales_are_shared_with_guest_pages()
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('locales', ['en' => 'English', 'de' => 'Deutsch'])
                ->where('locale', 'en'));
    }

    public function test_login_page_uses_the_locale_cookie()
    {
        $this->withUnencryptedCookie('locale', 'de')
            ->get(route('login'))
            ->assertSee('<html lang="de"', false)
            ->assertInertia(fn (Assert $page) => $page->where('locale', 'de'));
    }

    public function test_saved_user_locale_takes_precedence_over_the_cookie()
    {
        $this->actingAs(User::factory()->create(['locale' => 'de']))
            ->withUnencryptedCookie('locale', 'en')
            ->get(route('appearance.edit'))
            ->assertSee('<html lang="de"', false);
    }

    public function test_cookie_locale_is_used_when_the_user_has_no_saved_locale()
    {
        $this->actingAs(User::factory()->create(['locale' => null]))
            ->withUnencryptedCookie('locale', 'de')
            ->get(route('appearance.edit'))
            ->assertSee('<html lang="de"', false);
    }

    public function test_locale_conflict_is_shared_when_cookie_differs_from_the_saved_locale()
    {
        $this->actingAs(User::factory()->create(['locale' => 'de']))
            ->withUnencryptedCookie('locale', 'en')
            ->get(route('appearance.edit'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('localeConflict', ['account' => 'de', 'browser' => 'en']));
    }

    /**
     * @return array<string, array{?string, ?string}>
     */
    public static function noLocaleConflictProvider(): array
    {
        return [
            'same locale' => ['de', 'de'],
            'no saved locale' => [null, 'de'],
            'no cookie' => ['de', null],
            'unsupported cookie' => ['de', 'xx'],
        ];
    }

    #[DataProvider('noLocaleConflictProvider')]
    public function test_no_locale_conflict_is_shared(?string $saved, ?string $cookie)
    {
        $request = $this->actingAs(User::factory()->create(['locale' => $saved]));

        if ($cookie !== null) {
            $request->withUnencryptedCookie('locale', $cookie);
        }

        $request->get(route('appearance.edit'))
            ->assertInertia(fn (Assert $page) => $page->where('localeConflict', null));
    }

    public function test_guests_never_get_a_locale_conflict()
    {
        $this->withUnencryptedCookie('locale', 'de')
            ->get(route('login'))
            ->assertInertia(fn (Assert $page) => $page->where('localeConflict', null));
    }

    public function test_choosing_a_locale_resolves_the_conflict()
    {
        $user = User::factory()->create(['locale' => 'de']);

        $this->actingAs($user)
            ->withUnencryptedCookie('locale', 'en')
            ->from(route('appearance.edit'))
            ->patch(route('locale.update'), ['locale' => 'en'])
            ->assertPlainCookie('locale', 'en');

        $this->assertSame('en', $user->fresh()->locale);
    }

    public function test_pages_outside_the_web_middleware_group_use_the_cookie_locale()
    {
        $this->withUnencryptedCookie('locale', 'de')
            ->get('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertSee('Nicht gefunden');
    }

    public function test_user_locale_is_the_preferred_locale_for_notifications()
    {
        $user = User::factory()->make(['locale' => 'de']);

        $this->assertInstanceOf(HasLocalePreference::class, $user);
        $this->assertSame('de', $user->preferredLocale());
    }

    public function test_locale_is_shared_with_the_frontend()
    {
        $this->actingAs(User::factory()->create(['locale' => 'de']))
            ->get(route('appearance.edit'))
            ->assertInertia(fn (Assert $page) => $page->where('locale', 'de'));
    }
}
