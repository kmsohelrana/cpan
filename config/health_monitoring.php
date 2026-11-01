<?php

return [
    'refresh_interval_minutes' => (int) env('HEALTH_CHECK_INTERVAL_MINUTES', 5),
    'cache_ttl_minutes' => (int) env('HEALTH_CHECK_CACHE_TTL_MINUTES', 15), // longer than the interval so a missed run doesn't blank the dashboard
    'history_retention_days' => (int) env('HEALTH_HISTORY_RETENTION_DAYS', 365), // 1 year — payment-gateway audit trail retention

    'thresholds' => [
        // Each metric maps a value to green/yellow/orange/red bands.
        'storage' => ['warning' => 70, 'orange' => 85, 'critical' => 95], // % used
        'memory' => ['warning' => 70, 'orange' => 85, 'critical' => 95],  // % of PHP memory_limit
        'db_latency_ms' => ['warning' => 100, 'orange' => 300, 'critical' => 800],
        'queue_pending' => ['warning' => 100, 'orange' => 500, 'critical' => 2000],
    ],
];
