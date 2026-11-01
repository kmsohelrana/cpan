<?php

namespace App\Enums;

enum Language: string
{
    case BENGALI = 'bn';
    case ENGLISH = 'en';

    public function label(): string
    {
        return match ($this) {
            self::BENGALI => __('navigation.bengali'),
            self::ENGLISH => __('navigation.english'),
        };
    }

    public function flag(): string
    {
        return match ($this) {
            self::BENGALI => 'ud83cudde7ud83cudde9',
            self::ENGLISH => 'ud83cuddecud83cudde7',
        };
    }
}
