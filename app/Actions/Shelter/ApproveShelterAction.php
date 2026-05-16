<?php

declare(strict_types=1);

namespace App\Actions\Shelter;

use App\Enums\Shelter\ShelterStatus;
use App\Models\Shelter\Shelter;
use Illuminate\Support\Carbon;

class ApproveShelterAction
{
    public function execute(Shelter $shelter): Shelter
    {
        $shelter->status = ShelterStatus::Approved;
        $shelter->approved_at = Carbon::now();
        $shelter->save();

        return $shelter;
    }
}
