<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureTotpVerified
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

        if (app()->environment('local')) {
            return $next($request); // TOTP omijane lokalnie
        }

        if (!$user->totp_secret) {
            return redirect()->route('2fa.setup');
        }

        if (!session('2fa_verified')) {
            return redirect()->route('2fa.verify');
        }
    }

    return $next($request);
}
}
