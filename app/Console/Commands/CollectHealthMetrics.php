<?php

namespace App\Console\Commands;

use App\Services\HealthMonitoring\HealthMonitoringService;
use Illuminate\Console\Command;

class CollectHealthMetrics extends Command
{
    protected $signature = 'health:collect-metrics';
    protected $description = 'Collect infrastructure/database health metrics into cache and history';

    public function handle(HealthMonitoringService $healthMonitoringService): int
    {
        $results = $healthMonitoringService->collectAndStore();

        foreach ($results as $component => $result) {
            $this->line("  {$component}: {$result['status']->value} — {$result['message']}");
        }

        $this->info('Health metrics collected: ' . count($results) . ' component(s).');

        return self::SUCCESS;
    }
}
