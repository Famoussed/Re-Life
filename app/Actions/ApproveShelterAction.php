<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ShelterStatus;
use App\Models\Shelter;
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
