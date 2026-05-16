<?php

declare(strict_types=1);

namespace App\Enums\Animal;

enum NeedType: string
{
    case Food = 'food';
    case Vaccine = 'vaccine';
    case Illness = 'illness';

    public function label(): string
    {
        return match ($this) {
            self::Food => 'Mama',
            self::Vaccine => 'Aşı',
            self::Illness => 'Tedavi',
        };
    }
}
