<?php

namespace App\Enums;

enum CommonEnum
{
    case PER_PAGE;

    public function value(): int
    {
        return match ($this) {
            self::PER_PAGE => 10
        };
    }
}
