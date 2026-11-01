<?php

namespace App\Services\HealthMonitoring;

use App\Helpers\HealthThreshold;

class StorageHealthService
{
    public function check(): array
    {
        $path = storage_path();
        $total = disk_total_space($path);
        $free = disk_free_space($path);
        $used = $total - $free;
        $usagePercent = $total > 0 ? round(($used / $total) * 100, 2) : 0.0;

        $thresholds = config('health_monitoring.thresholds.storage');
        $status = HealthThreshold::resolveStatus($usagePercent, $thresholds);
        $color = HealthThreshold::resolveColor($usagePercent, $thresholds);

        return [
            'status' => $status,
            'value' => $usagePercent,
            'message' => "Storage at {$usagePercent}% usage",
            'payload' => [
                'total_gb' => round($total / 1024 / 1024 / 1024, 2),
                'used_gb' => round($used / 1024 / 1024 / 1024, 2),
                'free_gb' => round($free / 1024 / 1024 / 1024, 2),
                'usage_percent' => $usagePercent,
                'progress_color' => $color,
            ],
        ];
    }
}
