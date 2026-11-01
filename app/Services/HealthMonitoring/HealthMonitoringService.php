<?php

namespace App\Services\HealthMonitoring;

use App\Enums\HealthComponentEnum;
use App\Enums\HealthStatusEnum;
use App\Models\HealthMetricSnapshot;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class HealthMonitoringService
{
    private const CACHE_KEY = 'health_monitoring.snapshot';
    private const DIRECTORY_CACHE_KEY = 'health_monitoring.directory_sizes';

    public function __construct(
        private ApplicationHealthService $applicationHealthService,
        private ServerHealthService $serverHealthService,
        private DatabaseHealthService $databaseHealthService,
        private StorageHealthService $storageHealthService,
        private QueueHealthService $queueHealthService,
        private RedisHealthService $redisHealthService,
        private MailHealthService $mailHealthService,
        private DirectoryStorageService $directoryStorageService,
    ) {
    }

    /**
     * Run all health checks, cache the summary, persist one snapshot row per
     * component, and prune snapshots past the retention window.
     */
    public function collectAndStore(): array
    {
        $results = [
            HealthComponentEnum::APPLICATION->value => $this->safeCheck('application', fn () => $this->applicationHealthService->check()),
            HealthComponentEnum::SERVER->value => $this->safeCheck('server', fn () => $this->serverHealthService->check()),
            HealthComponentEnum::DATABASE->value => $this->safeCheck('database', fn () => $this->databaseHealthService->check()),
            HealthComponentEnum::STORAGE->value => $this->safeCheck('storage', fn () => $this->storageHealthService->check()),
            HealthComponentEnum::QUEUE->value => $this->safeCheck('queue', fn () => $this->queueHealthService->check()),
            HealthComponentEnum::REDIS->value => $this->safeCheck('redis', fn () => $this->redisHealthService->check()),
            HealthComponentEnum::MAIL->value => $this->safeCheck('mail', fn () => $this->mailHealthService->check()),
        ];

        $recordedAt = now();

        foreach ($results as $component => $result) {
            try {
                HealthMetricSnapshot::create([
                    'component' => $component,
                    'status' => $result['status'],
                    'value' => $result['value'],
                    'message' => $result['message'],
                    'payload' => $result['payload'],
                    'recorded_at' => $recordedAt,
                ]);
            } catch (Throwable $e) {
                // A single write failure (e.g. DB hiccup mid-run) must not stop the rest
                // of this run's rows from being persisted or the cache from being warmed.
                Log::error("health_monitoring: failed to persist snapshot for '{$component}'", ['exception' => $e]);
            }
        }

        try {
            Cache::put(self::CACHE_KEY, [
                'results' => $results,
                'collected_at' => $recordedAt->toDateTimeString(),
            ], now()->addMinutes(config('health_monitoring.cache_ttl_minutes')));
        } catch (Throwable $e) {
            Log::error('health_monitoring: failed to warm the summary cache', ['exception' => $e]);
        }

        try {
            $this->pruneOldSnapshots();
        } catch (Throwable $e) {
            Log::error('health_monitoring: failed to prune old snapshots', ['exception' => $e]);
        }

        try {
            // Recursive directory walk (vendor/node_modules) — deliberately only run here,
            // on the scheduled cycle, never on a page load.
            Cache::put(
                self::DIRECTORY_CACHE_KEY,
                $this->directoryStorageService->getDirectorySizes(),
                now()->addMinutes(config('health_monitoring.cache_ttl_minutes'))
            );
        } catch (Throwable $e) {
            Log::error('health_monitoring: failed to collect directory sizes', ['exception' => $e]);
        }

        return $results;
    }

    public function getDirectorySizes(): ?array
    {
        return Cache::get(self::DIRECTORY_CACHE_KEY);
    }

    /**
     * Runs one component's check(), isolating failures so one broken check (e.g. an
     * unexpected exception from a flaky service) can't stop the other components from
     * being collected — the whole command must never die because of a single check.
     */
    private function safeCheck(string $component, callable $check): array
    {
        try {
            return $check();
        } catch (Throwable $e) {
            Log::error("health_monitoring: '{$component}' check threw an exception", ['exception' => $e]);

            return [
                'status' => HealthStatusEnum::UNKNOWN,
                'value' => null,
                'message' => 'Check failed unexpectedly: ' . $e->getMessage(),
                'payload' => [],
            ];
        }
    }

    public function getCachedSummary(): ?array
    {
        return Cache::get(self::CACHE_KEY);
    }

    /**
     * Chronological (oldest-first) series of the last $limit snapshots for one component.
     * Includes status/color alongside the value so the same response can drive both the
     * line chart and the status timeline strip for that component.
     */
    public function getChartSeries(string $component, int $limit = 20): array
    {
        return HealthMetricSnapshot::query()
            ->where('component', $component)
            ->orderByDesc('recorded_at')
            ->limit($limit)
            ->get()
            ->sortBy('recorded_at')
            ->values()
            ->map(fn (HealthMetricSnapshot $snapshot) => [
                'recorded_at' => $snapshot->recorded_at->format('H:i'),
                'value' => $snapshot->value,
                'status' => $snapshot->status->value,
                'color' => $snapshot->status->color(),
                'message' => $snapshot->message,
            ])
            ->all();
    }

    /**
     * Paginated Recent Health Events, restricted to the last 24 hours.
     */
    public function getRecentEvents(int $perPage = 20)
    {
        return HealthMetricSnapshot::query()
            ->where('recorded_at', '>=', Carbon::now()->subHours(24))
            ->orderByDesc('recorded_at')
            ->paginate($perPage);
    }

    /**
     * Soft-deletes (not hard-deletes — HealthMetricSnapshot uses SoftDeletes) rows past
     * the retention window. Rows are marked deleted and recoverable, not physically
     * removed, per this system's audit-trail retention requirements.
     */
    private function pruneOldSnapshots(): void
    {
        HealthMetricSnapshot::query()
            ->where('recorded_at', '<', Carbon::now()->subDays(config('health_monitoring.history_retention_days')))
            ->delete();
    }
}
