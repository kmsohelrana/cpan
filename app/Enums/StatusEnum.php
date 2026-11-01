<?php

namespace App\Enums;

enum StatusEnum: int
{
    case ACTIVE = 1;
    case INACTIVE = 0;

    /**
     * Get the label for the status
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        };
    }

    /**
     * Get the color for the status
     */
    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger',
        };
    }

    /**
     * Get all possible values of the enum
     */
    public static function values(): array
    {
        return [
            self::ACTIVE,
            self::INACTIVE,
        ];
    }

    /**
     * Get options for dropdowns or selects
     */
    public static function options(): array
    {
        return [
            self::ACTIVE->value => 'Active',
            self::INACTIVE->value => 'Inactive',
        ];
    }

    /**
     * Get the badge HTML for the status
     */
    public function badge(): string
    {
        return "<span class='fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-{$this->color()}-light text-{$this->color()}'>{$this->label()}</span>";
    }


    /**
     * Get options for dropdowns or selects
     */
    public static function dropdown(): array
    {
        return [
            self::ACTIVE->value => self::ACTIVE->label(),
            self::INACTIVE->value => self::INACTIVE->label(),
        ];
    }
}
