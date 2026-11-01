<?php

namespace App\Services\HealthMonitoring;

use Illuminate\Support\Facades\Cache;

class LogSummaryService
{
    private const LEVELS = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];
    private const CACHE_TTL_SECONDS = 30;

    /**
     * @param string|null $pathOverride Test-only hook to point at a fixture file instead
     *                                  of today's real log path.
     */
    public function __construct(private ?string $pathOverride = null)
    {
    }

    /**
     * Counts log entries by level in today's daily log file. A full scan's cost scales
     * with file size, so — unlike the other live sections on this dashboard — this is
     * cached for a short window (30s) to avoid re-scanning a potentially large file on
     * every page hit; freshness within that window doesn't matter for a summary count.
     * Skipped when a $pathOverride is set (tests expect an uncached, immediate read).
     */
    public function getTodaySummary(): array
    {
        if ($this->pathOverride !== null) {
            return $this->scan($this->pathOverride);
        }

        return Cache::remember(
            'health_monitoring.log_summary.' . now()->format('Y-m-d'),
            self::CACHE_TTL_SECONDS,
            fn () => $this->scan($this->todayLogPath())
        );
    }

    private function scan(string $path): array
    {
        $counts = array_fill_keys(self::LEVELS, 0);

        if (!file_exists($path)) {
            return [
                'exists' => false,
                'path' => $path,
                'counts' => $counts,
                'total' => 0,
                'file_size_kb' => null,
            ];
        }

        $pattern = '/\.(' . implode('|', array_map('strtoupper', self::LEVELS)) . '):/';

        $handle = fopen($path, 'r');
        $total = 0;

        while (($line = fgets($handle)) !== false) {
            if (preg_match($pattern, $line, $matches)) {
                $counts[strtolower($matches[1])]++;
                $total++;
            }
        }

        fclose($handle);

        return [
            'exists' => true,
            'path' => $path,
            'counts' => $counts,
            'total' => $total,
            'file_size_kb' => round(filesize($path) / 1024, 1),
        ];
    }

    private function todayLogPath(): string
    {
        $prefix = env('LOG_FILE_NAME_PREFIX', '');

        return storage_path('logs/' . $prefix . 'laravel-' . now()->format('Y-m-d') . '.log');
    }
}
