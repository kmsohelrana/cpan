<?php

namespace App\Enums;

enum HealthStatusEnum: string
{
    case HEALTHY = 'healthy';
    case WARNING = 'warning';
    case CRITICAL = 'critical';
    case UNKNOWN = 'unknown';

    /**
     * Get the label for the status
     */
    public function label(): string
    {
        return match ($this) {
            self::HEALTHY => 'Healthy',
            self::WARNING => 'Warning',
            self::CRITICAL => 'Critical',
            self::UNKNOWN => 'Unknown',
        };
    }

    /**
     * Get the color for the status
     */
    public function color(): string
    {
        return match ($this) {
            self::HEALTHY => 'success',
            self::WARNING => 'warning',
            self::CRITICAL => 'danger',
            self::UNKNOWN => 'info',
        };
    }

    /**
     * Get the badge HTML for the status
     */
    public function badge(): string
    {
        return "<span class='fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-{$this->color()}-light text-{$this->color()}'>{$this->label()}</span>";
    }
}
