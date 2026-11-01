<?php

namespace App\Services\HealthMonitoring;

use App\Enums\HealthStatusEnum;
use App\Helpers\HealthThreshold;

class ServerHealthService
{
    public function check(): array
    {
        $memoryUsageBytes = memory_get_usage(true);
        $memoryLimitBytes = $this->parseMemoryLimit((string) ini_get('memory_limit'));
        $memoryUsagePercent = $memoryLimitBytes > 0
            ? round(($memoryUsageBytes / $memoryLimitBytes) * 100, 2)
            : null;

        // sys_getloadavg() is Linux/Mac only — false (and thus null here) on Windows.
        $loadAverage = function_exists('sys_getloadavg') ? sys_getloadavg() : false;

        $diskTotal = disk_total_space(storage_path());
        $diskFree = disk_free_space(storage_path());
        $diskUsedPercent = $diskTotal ? round((($diskTotal - $diskFree) / $diskTotal) * 100, 2) : null;

        $status = $memoryUsagePercent !== null
            ? HealthThreshold::resolveStatus($memoryUsagePercent, config('health_monitoring.thresholds.memory'))
            : HealthStatusEnum::UNKNOWN;

        return [
            'status' => $status,
            'value' => $memoryUsagePercent,
            'message' => $memoryUsagePercent !== null
                ? "PHP process memory at {$memoryUsagePercent}%"
                : 'Memory usage unavailable',
            'payload' => [
                'hostname' => gethostname() ?: 'Unknown',
                'os' => PHP_OS_FAMILY . ' (' . php_uname() . ')',
                'server_time' => now()->toDateTimeString(),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'load_average' => $loadAverage !== false ? $loadAverage : null,
                'memory_usage_percent' => $memoryUsagePercent,
                'memory_usage_mb' => round($memoryUsageBytes / 1024 / 1024, 2),
                'disk_used_percent' => $diskUsedPercent,
            ],
        ];
    }

    /**
     * Convert a php.ini memory_limit string (e.g. "512M", "-1") to bytes.
     * Returns 0 for "-1" (unlimited), since a percentage can't be computed against it.
     */
    private function parseMemoryLimit(string $limit): int
    {
        if ($limit === '-1') {
            return 0;
        }

        $unit = strtolower(substr($limit, -1));
        $value = (int) $limit;

        return match ($unit) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => (int) $limit,
        };
    }
}
