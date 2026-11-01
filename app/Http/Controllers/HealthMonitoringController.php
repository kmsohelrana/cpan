<?php

namespace App\Http\Controllers;

use App\Enums\HealthComponentEnum;
use App\Enums\PermissionEnum;
use App\Services\HealthMonitoring\ActiveSessionsService;
use App\Services\HealthMonitoring\ApiTrafficService;
use App\Services\HealthMonitoring\HealthMonitoringService;
use App\Services\HealthMonitoring\LogSummaryService;
use App\Services\HealthMonitoring\OutboundConnectivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Throwable;

class HealthMonitoringController extends Controller
{
    public function __construct(
        private HealthMonitoringService $healthMonitoringService,
        private ActiveSessionsService $activeSessionsService,
        private ApiTrafficService $apiTrafficService,
        private LogSummaryService $logSummaryService,
        private OutboundConnectivityService $outboundConnectivityService,
    ) {
    }

    public function index()
    {
        checkAuthPermission(PermissionEnum::HEALTH_MONITORING_VIEW);

        $title = 'Infrastructure Monitoring';

        $summary = $this->safely(fn () => $this->healthMonitoringService->getCachedSummary(), 'summary', null);
        $recentEvents = $this->safely(fn () => $this->healthMonitoringService->getRecentEvents(), 'recentEvents', new LengthAwarePaginator([], 0, 20));
        $activeSessions = $this->safely(fn () => $this->activeSessionsService->getActiveSessions(), 'activeSessions', new LengthAwarePaginator([], 0, 20));
        $logSummary = $this->safely(fn () => $this->logSummaryService->getTodaySummary(), 'logSummary', ['exists' => false, 'counts' => [], 'total' => 0, 'file_size_kb' => null]);
        $directorySizes = $this->safely(fn () => $this->healthMonitoringService->getDirectorySizes(), 'directorySizes', null);

        return view('health-monitoring.index', compact('title', 'summary', 'recentEvents', 'activeSessions', 'logSummary', 'directorySizes'));
    }

    public function chartData(string $component): JsonResponse
    {
        checkAuthPermission(PermissionEnum::HEALTH_MONITORING_VIEW);

        if (!HealthComponentEnum::tryFrom($component)) {
            return webResponse(Response::HTTP_NOT_FOUND, 'Unknown component.');
        }

        $series = $this->safely(fn () => $this->healthMonitoringService->getChartSeries($component), "chartData.{$component}", []);

        return webResponse(Response::HTTP_OK, null, $series);
    }

    public function apiTrafficByApp(): JsonResponse
    {
        checkAuthPermission(PermissionEnum::HEALTH_MONITORING_VIEW);
        $traffic = $this->safely(fn () => $this->apiTrafficService->getTrafficByApp(), 'apiTrafficByApp', ['labels' => [], 'series' => []]);
        return webResponse(Response::HTTP_OK, null, $traffic);
    }

    public function apiTrafficByRoute(): JsonResponse
    {
        checkAuthPermission(PermissionEnum::HEALTH_MONITORING_VIEW);
        $traffic = $this->safely(fn () => $this->apiTrafficService->getTrafficByRoute(), 'apiTrafficByRoute', ['labels' => [], 'series' => []]);
        return webResponse(Response::HTTP_OK, null, $traffic);
    }

    public function outboundConnectivity(): JsonResponse
    {
        checkAuthPermission(PermissionEnum::HEALTH_MONITORING_VIEW);
        $checks = $this->safely(fn () => $this->outboundConnectivityService->checkAll(), 'outboundConnectivity', []);
        return webResponse(Response::HTTP_OK, null, $checks);
    }

    /** Runs $callback, returning $fallback and logging instead of letting one section's
     *  exception take down the whole page. */
    private function safely(callable $callback, string $label, mixed $fallback): mixed
    {
        try {
            return $callback();
        } catch (Throwable $e) {
            Log::error("health_monitoring: '{$label}' failed to load", ['exception' => $e]);
            return $fallback;
        }
    }
}
