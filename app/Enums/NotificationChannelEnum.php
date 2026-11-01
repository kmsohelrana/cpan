<?php

namespace App\Enums;

enum NotificationChannelEnum: string
{
    case TRANSACTION_PUSH  = 'transaction_push';
    case TRANSACTION_SMS   = 'transaction_sms';
    case TRANSACTION_EMAIL = 'transaction_email';

    public function label(): string
    {
        return match ($this) {
            self::TRANSACTION_PUSH  => 'Transaction Push',
            self::TRANSACTION_SMS   => 'Transaction SMS',
            self::TRANSACTION_EMAIL => 'Transaction Email',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
