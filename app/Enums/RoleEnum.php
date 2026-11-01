<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SUPER_ADMIN = 'Super Admin';
    case ADMIN = 'Admin';
    case MANAGER = 'Manager';
    case OPERATOR = 'Operator';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMIN => 'Admin',
            self::MANAGER => 'Manager',
            self::OPERATOR => 'Operator',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
