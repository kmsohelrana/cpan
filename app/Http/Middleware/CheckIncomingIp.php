<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponseHelper;
use App\Helpers\LogHelper;
use Closure;

class CheckIncomingIp
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $checkIp = config('app.check_incoming_ip');

        if (!$checkIp) {
            return $next($request);
        }
        $allowedIps = json_decode($request->user()->allowed_ips ?? '[]', true);
        if (empty($allowedIps)) {
            return $next($request);
        }
        $incomingIp = LogHelper::incomingIp();
        if (!in_array($incomingIp, $allowedIps)) {
            $message = 'Access Denied: You are not allowed';
            LogHelper::logEntry(LogHelper::LOG_ERROR, 'Access Forbidden from IP: ' . $incomingIp, [
                'user_id' => $request->user()->id,
                'user_email' => $request->user()->email,
                'incoming_ip' => $incomingIp,
                'allowed_ips' => $allowedIps,
                'user_agent' => $request->header('User-Agent'),
            ], $request->header('Log-Trace-ID'));
            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiResponseHelper::forbidden($message . ' ' . $incomingIp);
            }
            abort(403, $message . ' ' . $incomingIp);
        }
        return $next($request);
    }
}
