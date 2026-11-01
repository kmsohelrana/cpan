<?php

namespace App\Enums;

enum NotificationStatusEnum: int
{
    case PENDING = 1;
    case SENT = 2;
    case FAILED = 3;

    public function label(): string
    {
        return match ($this) {
            static::PENDING => 'Pending',
            static::SENT => 'Sent',
            static::FAILED => 'Failed',
        };
    }

    public static function toArray(): array
    {
        return array_map(fn($status) => [
            'value' => $status->value,
            'label' => $status->label()
        ], self::cases());
    }
}
