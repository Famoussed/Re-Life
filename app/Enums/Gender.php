<?php

declare(strict_types=1);

namespace App\Enums;

enum Gender: string
{
    case Male = 'male';
    case Female = 'female';
    case Unknown = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::Male => 'Erkek',
            self::Female => 'Dişi',
            self::Unknown => 'Bilinmiyor',
        };
    }
}
