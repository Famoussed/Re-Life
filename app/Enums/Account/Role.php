<?php

declare(strict_types=1);

namespace App\Enums\Account;

enum Role: string
{
    case Superadmin = 'superadmin';
    case Admin = 'admin';
    case User = 'user';
    case Veterinarian = 'veterinarian';

    public function label(): string
    {
        return match ($this) {
            self::Superadmin => 'Platform Yöneticisi',
            self::Admin => 'Barınak Yöneticisi',
            self::User => 'Bağışçı',
            self::Veterinarian => 'Veteriner',
        };
    }
}
