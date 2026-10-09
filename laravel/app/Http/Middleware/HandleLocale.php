<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class HandleLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        foreach ([$this->userLocale($request), $request->cookie('locale')] as $locale) {
            if (self::isAvailable($locale)) {
                App::setLocale($locale);
                break;
            }
        }

        return $next($request);
    }

    public static function isAvailable(mixed $locale): bool
    {
        return is_string($locale) && array_key_exists($locale, config('app.available_locales'));
    }

    private function userLocale(Request $request): ?string
    {
        return $request->hasSession() ? $request->user()?->locale : null;
    }
}
