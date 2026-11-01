<?php

namespace App\Helpers;

use App\Enums\HealthStatusEnum;

class HealthThreshold
{
    /**
     * 0 = below warning, 1 = warning, 2 = orange, 3 = critical.
     */
    private static function tier(float $value, array $thresholds): int
    {
        if ($value >= $thresholds['critical']) {
            return 3;
        }

        if ($value >= $thresholds['orange']) {
            return 2;
        }

        if ($value >= $thresholds['warning']) {
            return 1;
        }

        return 0;
    }

    /**
     * Four-tier color for progress bars: green/yellow/orange/red.
     */
    public static function resolveColor(float $value, array $thresholds): string
    {
        return match (self::tier($value, $thresholds)) {
            3 => 'danger',
            2 => 'orange',
            1 => 'warning',
            default => 'success',
        };
    }

    /**
     * Three-tier status for summary badges (orange collapses into warning).
     */
    public static function resolveStatus(float $value, array $thresholds): HealthStatusEnum
    {
        return match (self::tier($value, $thresholds)) {
            3 => HealthStatusEnum::CRITICAL,
            2, 1 => HealthStatusEnum::WARNING,
            default => HealthStatusEnum::HEALTHY,
        };
    }
}
