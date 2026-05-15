<?php

declare(strict_types=1);

namespace App\Enums;

enum AnimalSpecies: string
{
    case Cat = 'cat';
    case Dog = 'dog';
    case Kitten = 'kitten';
    case Puppy = 'puppy';

    public function label(): string
    {
        return match ($this) {
            self::Cat => 'Kedi',
            self::Dog => 'Köpek',
            self::Kitten => 'Yavru Kedi',
            self::Puppy => 'Yavru Köpek',
        };
    }
}
