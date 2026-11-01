<?php

namespace App\Services\HealthMonitoring;

use App\Enums\HealthStatusEnum;

class ApplicationHealthService
{
    /**
     * Application-level info is always "healthy" if this code is running at all —
     * there's no failure mode to detect here, only informational fields to report.
     */
    public function check(): array
    {
        return [
            'status' => HealthStatusEnum::HEALTHY,
            'value' => null,
            'message' => 'Application running normally',
            'payload' => [
                'app_version' => config('app.version', 'N/A'), // not tracked anywhere in this codebase today
                'laravel_version' => app()->version(),
                'php_version' => PHP_VERSION,
                'timezone' => config('app.timezone'),
                'cache_driver' => config('cache.default'),
                'session_driver' => config('session.driver'),
                'queue_driver' => config('queue.default'),
                'app_env' => config('app.env'),
                'app_debug' => config('app.debug'),
            ],
        ];
    }
}
