<?php

namespace App\Http\Middleware;

use Closure;

class CheckPasswordExpiryMiddleware
{
    public function handle($request, Closure $next)
    {
        if (
            config('password.enable_password_expiration', 'on') == 'on' &&
            !is_null(auth()->user()->password_changed_at) &&
            auth()->user()->password_changed_at->diffInDays(now()) >= config('password.expires_after_days', 90) &&
            !in_array($request->route()->getName(), ['change_password.index', 'change_password.update'])
        ) {
            return redirect()->route('change_password.index')->with('warning', 'Your password has expired. You must change your password to continue.');
        }

        return $next($request);
    }
}
