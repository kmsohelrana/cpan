<?php

namespace App\Enums;

enum HealthComponentEnum: string
{
    case APPLICATION = 'application';
    case SERVER = 'server';
    case DATABASE = 'database';
    case STORAGE = 'storage';
    case QUEUE = 'queue';
    case REDIS = 'redis';
    case MAIL = 'mail';

    /**
     * Get the label for the component
     */
    public function label(): string
    {
        return match ($this) {
            self::APPLICATION => 'Application',
            self::SERVER => 'Server',
            self::DATABASE => 'Database',
            self::STORAGE => 'Storage',
            self::QUEUE => 'Queue',
            self::REDIS => 'Redis',
            self::MAIL => 'Mail',
        };
    }
}
