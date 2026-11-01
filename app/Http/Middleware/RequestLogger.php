<?php

namespace App\Http\Middleware;

use App\Enums\Language;
use App\Helpers\LogHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $logTraceID = $request->header('Third-Party-Log-Trace-ID') ?: LogHelper::generateLogTraceId();
        $request->headers->set('Log-Trace-ID', $logTraceID);

        if ($request->is('api/*') && $request->user() && $request->header('Accept-Language')) {
            $acceptedLanguage = $request->header('Accept-Language');
            $availableLanguages = array_column(Language::cases(), 'value');
            if (in_array($acceptedLanguage, $availableLanguages) && $acceptedLanguage != $request->user()->language) {
                $request->user()->update(['language' => $acceptedLanguage]);
            }
        }

        return $next($request);
    }
}
