<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\LocaleUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class AppearanceController extends Controller
{
    public function edit(): Response
    {
        return inertia('settings/appearance');
    }

    public function updateLocale(LocaleUpdateRequest $request): RedirectResponse
    {
        $locale = $request->validated('locale');

        $request->user()?->update(['locale' => $locale]);

        return back()->withCookie(cookie()->forever('locale', $locale));
    }
}
