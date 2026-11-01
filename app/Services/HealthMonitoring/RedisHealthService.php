<?php

namespace App\Services\HealthMonitoring;

use App\Enums\HealthStatusEnum;
use Illuminate\Support\Facades\Redis;
use Throwable;

class RedisHealthService
{
    public function check(): array
    {
        try {
            $start = microtime(true);
            $pong = Redis::connection()->ping();
            $responseTimeMs = round((microtime(true) - $start) * 1000, 2);

            $info = Redis::connection()->info('memory');
            $usedMemory = $info['used_memory_human'] ?? null;

            return [
                'status' => HealthStatusEnum::HEALTHY,
                'value' => $responseTimeMs,
                'message' => "Redis responded in {$responseTimeMs}ms",
                'payload' => [
                    'connected' => true,
                    'ping' => is_string($pong) ? $pong : 'PONG',
                    'response_time_ms' => $responseTimeMs,
                    'memory_usage' => $usedMemory ?? 'Unknown',
                ],
            ];
        } catch (Throwable $e) {
            return [
                'status' => HealthStatusEnum::UNKNOWN,
                'value' => null,
                'message' => 'Redis not configured or unreachable: ' . $e->getMessage(),
                'payload' => [
                    'connected' => false,
                    'ping' => null,
                    'response_time_ms' => null,
                    'memory_usage' => null,
                ],
            ];
        }
    }
}
