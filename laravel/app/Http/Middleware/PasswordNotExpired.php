<?php

namespace App\Http\Middleware;

use App\Contracts\Auth\MustRenewPassword;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PasswordNotExpired
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if($user instanceof MustRenewPassword && !$user->hasValidPassword()){
            if($request->expectsJson()){
                abort(403, 'Your password has expired.');
            }

            return redirect()->guest(route('password.expired'));
        }

        return $next($request);
    }
}
