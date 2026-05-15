<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\ApproveShelterAction;
use App\Enums\ShelterStatus;
use App\Models\Shelter;
use App\Notifications\AdminRegistrationStatusNotification;
use Illuminate\Support\Facades\DB;

class ShelterService
{
    public function __construct(private ApproveShelterAction $approveShelter)
    {
    }

    public function approve(Shelter $shelter): void
    {
        DB::transaction(function () use ($shelter) {
            $this->approveShelter->execute($shelter);
            $shelter->admin?->notify(new AdminRegistrationStatusNotification($shelter, true));
        });
    }

    public function reject(Shelter $shelter): void
    {
        DB::transaction(function () use ($shelter) {
            $shelter->status = ShelterStatus::Rejected;
            $shelter->save();
            $shelter->admin?->notify(new AdminRegistrationStatusNotification($shelter, false));
        });
    }

    public function suspend(Shelter $shelter): void
    {
        $shelter->status = ShelterStatus::Suspended;
        $shelter->save();
    }

    public function activate(Shelter $shelter): void
    {
        $shelter->status = ShelterStatus::Approved;
        $shelter->save();
    }
}
