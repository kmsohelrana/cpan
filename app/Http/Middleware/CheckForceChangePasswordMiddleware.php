<?php

namespace App\Http\Middleware;

use Closure;

class CheckForceChangePasswordMiddleware
{
    public function handle($request, Closure $next)
    {
        if (
            config('password.enable_force_password_change', 'on') == 'on' &&
            auth()->user()->change_password == 1 && !in_array($request->route()->getName(), ['password.index', 'password.change'])
        ) {
            return redirect()->route('password.index')->with('warning', 'You must change your password to continue.');
        }

        return $next($request);
    }
}
