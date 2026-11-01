<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogHelper
{
    public const LOG_INFO = 'info';
    public const LOG_EMERGENCY = 'emergency';
    public const LOG_CRITICAL = 'critical';
    public const LOG_NOTICE = 'notice';
    public const LOG_ERROR = 'error';
    public const LOG_WARNING = 'warning';
    public const LOG_DEBUG = 'debug';

    public static function generateLogTraceId(): string
    {
        return request()->header('Log-Trace-ID') ?? ('AP' . time() . rand(0, 9));
    }

    public static function incomingIp(): string
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? $_SERVER['HTTP_CLIENT_IP'] ?? 'N/A';
    }

    public static function logEntry(string $logType = 'info', string $msg = '', $logInfo = null, $logTraceId = null, string $channel = 'daily'): void
    {
        $logTraceID = $logTraceId ?? self::generateLogTraceId();

        $validLogTypes = [
            self::LOG_INFO,
            self::LOG_EMERGENCY,
            self::LOG_CRITICAL,
            self::LOG_NOTICE,
            self::LOG_ERROR,
            self::LOG_WARNING,
            self::LOG_DEBUG,
        ];

        if (in_array($logType, $validLogTypes)) {
            Log::channel($channel)->{$logType}('Log Trace ID: ' . $logTraceID . ' - ' . $msg, $logInfo !== null ? [$logInfo] : []);
        }
    }

    public static function landingLog(Request $request, $title): void
    {
        $ip = self::incomingIp();
        $url = $request->fullUrl();
        $method = $request->method();

        $request->merge(['incoming_ip' => $ip]);
        self::logEntry(self::LOG_INFO, $title, $ip);

        $ignoreParams = ['pin', 'password', 'card_no', 'incoming_ip', 'fcm_token'];
        $rawText = $request->except($ignoreParams);
        $logMessage = $rawText ? "Incoming Request: {$method} {$url}" : "Incoming Request: {$method} {$url} - empty";
        self::logEntry(self::LOG_INFO, $logMessage, $rawText);
    }
}
