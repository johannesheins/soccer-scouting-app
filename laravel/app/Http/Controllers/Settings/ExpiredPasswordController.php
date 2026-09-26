<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\PasswordUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ExpiredPasswordController extends Controller
{
    /**
     * Show the page that asks the user to renew their expired password.
     */
    public function edit(): Response
    {
        return Inertia::render('auth/password-expired');
    }

    /**
     * Renew the user's expired password.
     */
    public function update(PasswordUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->renewPassword($request->password);

        return redirect()->intended(route('dashboard'));
    }
}
