<?php

namespace App\Services\HealthMonitoring;

use App\Models\UserSecurityAudit;
use Illuminate\Support\Carbon;

class ApiTrafficService
{
    /**
     * Hourly request volume for the last $hours, split by app (CRM vs Merchant).
     */
    public function getTrafficByApp(int $hours = 24): array
    {
        $rows = UserSecurityAudit::query()
            ->where('created_at', '>=', now()->subHours($hours))
            ->get(['api_type', 'created_at']);

        return $this->bucketByHour($rows, fn ($row) => $row->api_type === 'merchant_api' ? 'Merchant API' : 'CRM API', $hours);
    }

    /**
     * Hourly request volume for the last $hours, one series per endpoint taken from
     * api_name. Covers every API hit in the window — both the CRM and Merchant apps,
     * and every endpoint, with no top-N cap and no "Other" bucket.
     */
    public function getTrafficByRoute(int $hours = 24): array
    {
        $rows = UserSecurityAudit::query()
            ->where('created_at', '>=', now()->subHours($hours))
            ->get(['api_name', 'created_at'])
            ->map(fn ($row) => (object) [
                'endpoint' => $this->endpointLabel($row->api_name),
                'created_at' => $row->created_at,
            ]);

        $traffic = $this->bucketByHour($rows, fn ($row) => $row->endpoint, $hours);

        // Busiest endpoint first, so the legend leads with what actually matters.
        $traffic['series'] = collect($traffic['series'])
            ->sortByDesc(fn ($counts) => array_sum($counts))
            ->all();

        return $traffic;
    }

    /**
     * Buckets rows into hourly slots (oldest-first) and sums a count per label per slot.
     */
    private function bucketByHour(iterable $rows, callable $labelResolver, int $hours): array
    {
        $bucketKeys = [];
        for ($i = $hours - 1; $i >= 0; $i--) {
            $bucketKeys[] = now()->subHours($i)->format('Y-m-d H:00');
        }

        $series = [];
        foreach ($rows as $row) {
            $hourKey = $row->created_at->format('Y-m-d H:00');
            $label = $labelResolver($row);
            $series[$label][$hourKey] = ($series[$label][$hourKey] ?? 0) + 1;
        }

        return [
            'labels' => array_map(fn ($key) => Carbon::createFromFormat('Y-m-d H:00', $key)->format('H:00'), $bucketKeys),
            'series' => collect($series)
                ->map(fn ($counts) => array_map(fn ($key) => $counts[$key] ?? 0, $bucketKeys))
                ->all(),
        ];
    }

    /**
     * Normalises a stored api_name (a raw request URI) into an endpoint label:
     * "/api/v1/merchant/transactions/42?page=2" -> "/merchant/transactions/{id}".
     * The query string and the constant /api/v1 prefix carry no signal, and numeric
     * segments collapse so one endpoint doesn't fan out into a series per record id.
     */
    private function endpointLabel(?string $apiName): string
    {
        $path = parse_url((string) $apiName, PHP_URL_PATH) ?: (string) $apiName;

        $segments = array_values(array_filter(explode('/', $path), fn ($segment) => $segment !== ''));

        if (($segments[0] ?? null) === 'api') {
            array_shift($segments);
        }

        if (preg_match('/^v\d+$/', $segments[0] ?? '')) {
            array_shift($segments);
        }

        $segments = array_map(
            fn ($segment) => preg_match('/^\d+$/', $segment) ? '{id}' : $segment,
            $segments
        );

        return $segments ? '/' . implode('/', $segments) : '/';
    }
}
