<?php

declare(strict_types=1);

namespace App\Enums\Shelter;

enum ShelterStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Onay Bekliyor',
            self::Approved => 'Onaylı',
            self::Rejected => 'Reddedildi',
            self::Suspended => 'Askıya Alındı',
        };
    }
}
