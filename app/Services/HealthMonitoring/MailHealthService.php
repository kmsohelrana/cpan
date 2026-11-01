<?php

namespace App\Services\HealthMonitoring;

use App\Enums\HealthStatusEnum;

class MailHealthService
{
    public function check(): array
    {
        $mailer = config('mail.default');

        if ($mailer !== 'smtp') {
            return [
                'status' => HealthStatusEnum::HEALTHY,
                'value' => null,
                'message' => "Using '{$mailer}' mail driver (not SMTP — no connection to test)",
                'payload' => [
                    'mailer' => $mailer,
                    'connected' => null,
                    'host' => null,
                    'port' => null,
                    'response_time_ms' => null,
                ],
            ];
        }

        $host = config('mail.mailers.smtp.host');
        $port = config('mail.mailers.smtp.port');

        $start = microtime(true);
        $connection = @fsockopen($host, $port, $errno, $errstr, 3);
        $responseTimeMs = round((microtime(true) - $start) * 1000, 2);

        if (!$connection) {
            return [
                'status' => HealthStatusEnum::CRITICAL,
                'value' => null,
                'message' => "Could not reach SMTP server {$host}:{$port} ({$errstr})",
                'payload' => [
                    'mailer' => $mailer,
                    'connected' => false,
                    'host' => $host,
                    'port' => $port,
                    'response_time_ms' => null,
                ],
            ];
        }

        fclose($connection);

        return [
            'status' => HealthStatusEnum::HEALTHY,
            'value' => $responseTimeMs,
            'message' => "SMTP server {$host}:{$port} reachable in {$responseTimeMs}ms",
            'payload' => [
                'mailer' => $mailer,
                'connected' => true,
                'host' => $host,
                'port' => $port,
                'response_time_ms' => $responseTimeMs,
            ],
        ];
    }
}
