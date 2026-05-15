<?php

declare(strict_types=1);

namespace App\Enums;

enum NeedStatus: string
{
    case Active = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Aktif',
            self::Completed => 'Tamamlandı',
            self::Cancelled => 'İptal Edildi',
        };
    }
}
