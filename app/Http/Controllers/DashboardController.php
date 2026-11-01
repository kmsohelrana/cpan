<?php

namespace App\Http\Controllers;

use App\Enums\StatusEnum;
use App\Models\GlobalSetting;
use App\Models\User;
use App\Services\HealthMonitoring\HealthMonitoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Throwable;

class DashboardController extends Controller
{
    public function __construct(
        protected ?HealthMonitoringService $healthMonitoringService = null
    ) {
    }

    /**
     * Show the administrator dashboard.
     */
    public function index()
    {
        $title = 'Dashboard';

        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'app_env' => app()->environment(),
            'cache_driver' => config('cache.default'),
            'queue_connection' => config('queue.default'),
            'db_driver' => config('database.default'),
        ];

        return view('dashboard', compact('title', 'systemInfo'));
    }

    /**
     * Get counts of administrative entities for dashboard cards.
     */
    public function counts(): JsonResponse
    {
        // 1. Users count
        $users = User::toBase()->selectRaw(
            'COUNT(*) as total,
             SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as active,
             SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as inactive',
            [StatusEnum::ACTIVE->value, StatusEnum::INACTIVE->value]
        )->first();

        // 2. Roles count
        $rolesCount = Role::where('guard_name', 'web')->count();

        // 3. Global Settings count
        $settingsCount = GlobalSetting::count();

        // 4. Infrastructure Health status
        $healthStatus = 'OK';
        $healthMessage = 'All systems normal';

        try {
            if ($this->healthMonitoringService) {
                $summary = $this->healthMonitoringService->getCachedSummary();
                if ($summary && !empty($summary['results'])) {
                    $hasCritical = false;
                    $hasWarning = false;
                    foreach ($summary['results'] as $res) {
                        $st = strtoupper($res['status'] ?? 'OK');
                        if (in_array($st, ['CRITICAL', 'FAIL', 'DOWN', 'ERROR'])) {
                            $hasCritical = true;
                        } elseif (in_array($st, ['WARNING', 'WARN', 'DEGRADED'])) {
                            $hasWarning = true;
                        }
                    }

                    if ($hasCritical) {
                        $healthStatus = 'CRITICAL';
                        $healthMessage = 'Action required on infrastructure';
                    } elseif ($hasWarning) {
                        $healthStatus = 'WARNING';
                        $healthMessage = 'Some components degraded';
                    } else {
                        $healthStatus = 'OK';
                        $healthMessage = 'All components healthy';
                    }
                }
            }
        } catch (Throwable) {
            $healthStatus = 'UNKNOWN';
            $healthMessage = 'Health status check pending';
        }

        return response()->json([
            'users' => [
                'total' => (int) ($users->total ?? 0),
                'active' => (int) ($users->active ?? 0),
                'inactive' => (int) ($users->inactive ?? 0),
            ],
            'roles' => [
                'total' => $rolesCount,
            ],
            'settings' => [
                'total' => $settingsCount,
            ],
            'health' => [
                'status' => $healthStatus,
                'message' => $healthMessage,
            ],
        ]);
    }
}
