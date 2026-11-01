<?php

namespace App\Services\HealthMonitoring;

use App\Enums\HealthStatusEnum;
use App\Helpers\HealthThreshold;
use Illuminate\Support\Facades\DB;
use Throwable;

class DatabaseHealthService
{
    public function check(): array
    {
        try {
            $start = microtime(true);
            $connection = DB::connection();
            $connection->getPdo();
            if ($connection->getDriverName() === 'oracle') {
                $connection->select('SELECT 1 FROM DUAL');
            } else {
                $connection->select('SELECT 1');
            }
            $responseTimeMs = round((microtime(true) - $start) * 1000, 2);
        } catch (Throwable $e) {
            return [
                'status' => HealthStatusEnum::CRITICAL,
                'value' => null,
                'message' => 'Database connection failed: ' . $e->getMessage(),
                'payload' => [
                    'connected' => false,
                    'database_name' => null,
                    'database_version' => null,
                    'response_time_ms' => null,
                    'database_size_mb' => null,
                    'active_connections' => null,
                    'slow_queries' => null,
                ],
            ];
        }

        $driver = DB::connection()->getDriverName();
        $databaseName = DB::connection()->getDatabaseName();

        $databaseVersion = match ($driver) {
            'mysql' => DB::selectOne('SELECT VERSION() AS version')->version ?? 'Unknown',
            'sqlite' => DB::selectOne('SELECT sqlite_version() AS version')->version ?? 'Unknown',
            'oracle' => DB::selectOne('SELECT BANNER AS version FROM V$VERSION WHERE ROWNUM = 1')->version ?? 'Unknown',
            default => 'Unknown',
        };

        $databaseSizeMb = null;
        $activeConnections = null;
        $slowQueries = null;

        if ($driver === 'mysql') {
            $sizeRow = DB::selectOne(
                'SELECT SUM(data_length + index_length) AS size FROM information_schema.tables WHERE table_schema = ?',
                [$databaseName]
            );
            $databaseSizeMb = $sizeRow && $sizeRow->size !== null ? round($sizeRow->size / 1024 / 1024, 2) : null;

            $connectionsRow = DB::selectOne("SHOW STATUS LIKE 'Threads_connected'");
            $activeConnections = $connectionsRow->Value ?? null;

            $slowQueriesRow = DB::selectOne("SHOW STATUS LIKE 'Slow_queries'");
            $slowQueries = $slowQueriesRow->Value ?? null;
        }

        $thresholds = config('health_monitoring.thresholds.db_latency_ms');
        $status = HealthThreshold::resolveStatus($responseTimeMs, $thresholds);

        return [
            'status' => $status,
            'value' => $responseTimeMs,
            'message' => "Database responded in {$responseTimeMs}ms",
            'payload' => [
                'connected' => true,
                'database_name' => $databaseName,
                'database_version' => $databaseVersion,
                'response_time_ms' => $responseTimeMs,
                'database_size_mb' => $databaseSizeMb,
                'active_connections' => $activeConnections,
                'slow_queries' => $slowQueries,
            ],
        ];
    }
}
