<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Helpers\LogHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class HealthCheckController extends Controller
{
    /**
     * Application Health & Dependency Check.
     *
     * @return JsonResponse
     */
    public function heartbeat(): JsonResponse
    {
        $hasFailure = false;

        $checks = [
            'application' => 'OK',
            'mysql' => [
                'status' => 'OK',
            ],
        ];

        $logTraceId = LogHelper::generateLogTraceId();

        // 1. Check MySQL
        try {
            DB::statement('SELECT 1');
        } catch (Throwable $e) {
            $hasFailure = true;
            $checks['mysql']['status'] = 'FAIL';

            LogHelper::logEntry(
                LogHelper::LOG_EMERGENCY,
                'Heartbeat Failure: MySQL connection lost.',
                ['error' => $e->getMessage()],
                $logTraceId
            );

        }

        if ($hasFailure) {
            $checks['application'] = 'FAIL';
        }

        // Determine HTTP status code dynamically
        $httpStatusCode = $hasFailure
            ? Response::HTTP_SERVICE_UNAVAILABLE  // HTTP 503
            : Response::HTTP_OK;                 // HTTP 200

        $data = [
            'service' => 'acquiring-platform',
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment(),
            'checks' => $checks,
        ];

        LogHelper::logEntry('debug', 'Heartbeat check response',
            ['http_code' => $httpStatusCode, 'checks' => $checks], $logTraceId);

        return response()->json([
            'data' => $data,
            'code' => (string)$httpStatusCode,
            'message' => $hasFailure ? 'SERVICE UNAVAILABLE' : 'OK',
        ], $httpStatusCode);
    }
}
