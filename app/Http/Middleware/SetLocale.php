<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Priority order for locale determination:
        // 1. URL parameter (?lang=en or ?lang=bn)
        // 2. Session value
        // 3. Authenticated user preference
        // 4. Request header (Accept-Language)
        // 5. Default to Bangla (bn)

        $locale = null;

        // 1. Check URL parameter
        if ($request->has('lang')) {
            $requestedLocale = $request->get('lang');
            if (in_array($requestedLocale, ['bn', 'en'])) {
                $locale = $requestedLocale;
                // Store in session for subsequent requests
                Session::put('locale', $locale);
            }
        }

        // 2. Check session
        if (! $locale && Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            if (in_array($sessionLocale, ['bn', 'en'])) {
                $locale = $sessionLocale;
            }
        }

        // 3. Check authenticated user preference
        if (! $locale && auth()->check() && auth()->user()->language) {
            $userLocale = auth()->user()->language->value ?? auth()->user()->language;
            if (in_array($userLocale, ['bn', 'en'])) {
                $locale = $userLocale;
                // Store in session
                Session::put('locale', $locale);
            }
        }

        // 4. Check Accept-Language header
        if (! $locale && $request->header('Accept-Language')) {
            $headerLocale = $request->header('Accept-Language');
            if (in_array($headerLocale, ['bn', 'en'])) {
                $locale = $headerLocale;
            }
        }

        // 5. Default to English
        if (! $locale) {
            $locale = 'en';
        }

        // Set the application locale
        App::setLocale($locale);

        // Set locale in session if not already set
        if (! Session::has('locale')) {
            Session::put('locale', $locale);
        }

        return $next($request);
    }
}
