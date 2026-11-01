<?php

namespace App\Services\HealthMonitoring;

class OutboundConnectivityService
{
    public function __construct(private int $timeoutSeconds = 2)
    {
    }

    /**
     * A "telnet check" (raw TCP connect, no auth, no request body) against every
     * outbound integration this app calls out to. Read live on demand (via its own
     * AJAX endpoint), not on the scheduled cache cycle.
     */
    public function checkAll(): array
    {
        $smtpHost = config('mail.mailers.smtp.host');
        $smtpPort = (int) (config('mail.mailers.smtp.port') ?? 587);

        return array_filter([
            $this->checkUrl('SMS Gateway (iSMS Plus)', config('ismsplus.base_url')),
            $smtpHost ? $this->checkHost('Mail Server (SMTP)', $smtpHost, $smtpPort) : null,
            $this->checkHost('Firebase — Push Notifications (FCM)', 'fcm.googleapis.com', 443),
            $this->checkHost('Firebase — OAuth Token Exchange', 'oauth2.googleapis.com', 443),
            $this->checkHost('Public Internet Connectivity', 'google.com', 443),
        ]);
    }

    private function checkUrl(string $label, ?string $url): array
    {
        if (!$url) {
            return $this->notConfigured($label);
        }

        $parts = parse_url($url);
        $host = $parts['host'] ?? null;

        if (!$host) {
            return $this->notConfigured($label);
        }

        $port = $parts['port'] ?? (($parts['scheme'] ?? 'http') === 'https' ? 443 : 80);

        return $this->checkHost($label, $host, $port);
    }

    private function checkHost(string $label, string $host, int $port): array
    {
        $start = microtime(true);
        $connection = @fsockopen($host, $port, $errno, $errstr, $this->timeoutSeconds);
        $responseTimeMs = round((microtime(true) - $start) * 1000, 2);
        $checkedAt = now()->toDateTimeString();

        if (!$connection) {
            return [
                'label' => $label,
                'target' => "{$host}:{$port}",
                'connected' => false,
                'response_time_ms' => null,
                'error' => $errstr ?: "Connection failed (code {$errno})",
                'checked_at' => $checkedAt,
            ];
        }

        fclose($connection);

        return [
            'label' => $label,
            'target' => "{$host}:{$port}",
            'connected' => true,
            'response_time_ms' => $responseTimeMs,
            'error' => null,
            'checked_at' => $checkedAt,
        ];
    }

    private function notConfigured(string $label): array
    {
        return [
            'label' => $label,
            'target' => 'Not configured',
            'connected' => null,
            'response_time_ms' => null,
            'error' => 'No URL or host set in configuration',
            'checked_at' => now()->toDateTimeString(),
        ];
    }
}
