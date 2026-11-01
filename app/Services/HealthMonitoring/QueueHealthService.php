<?php

namespace App\Services\HealthMonitoring;

use App\Enums\HealthStatusEnum;
use App\Helpers\HealthThreshold;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class QueueHealthService
{
    public function check(): array
    {
        $driver = config('queue.default');

        $pendingJobs = $driver === 'database' ? DB::table('jobs')->count() : null;
        $failedJobs = DB::table('failed_jobs')->count();

        $heartbeat = Cache::get('health_monitoring.scheduler_heartbeat');
        $schedulerStatus = $heartbeat
            ? (now()->diffInMinutes($heartbeat) <= 5 ? 'Running' : 'Stale (last run ' . $heartbeat->diffForHumans() . ')')
            : 'Unknown (no heartbeat recorded yet)';

        $status = $pendingJobs !== null
            ? HealthThreshold::resolveStatus((float) $pendingJobs, config('health_monitoring.thresholds.queue_pending'))
            : HealthStatusEnum::UNKNOWN;

        return [
            'status' => $status,
            'value' => $pendingJobs !== null ? (float) $pendingJobs : null,
            'message' => $pendingJobs !== null
                ? "{$pendingJobs} pending job(s)"
                : "Pending job count not tracked for driver '{$driver}'",
            'payload' => [
                'driver' => $driver,
                'pending_jobs' => $pendingJobs,
                'failed_jobs' => $failedJobs,
                'running_workers' => 'Not tracked',
                'scheduler_status' => $schedulerStatus,
                'supervisor_status' => 'Not available',
            ],
        ];
    }
}
