<?php

declare(strict_types=1);

namespace App\Livewire\Shelter;

use App\Enums\Shelter\ShelterStatus;
use App\Models\Shelter\Shelter;
use App\Services\Shelter\ShelterService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.panel')]
#[Title('Barınak Onayları — Re·Life')]
class ShelterApprovals extends Component
{
    public function approve(int $shelterId): void
    {
        $shelter = Shelter::where('status', ShelterStatus::Pending)->findOrFail($shelterId);

        app(ShelterService::class)->approve($shelter);

        session()->flash('status', "“{$shelter->name}” barınağı onaylandı.");
    }

    public function reject(int $shelterId): void
    {
        $shelter = Shelter::where('status', ShelterStatus::Pending)->findOrFail($shelterId);

        app(ShelterService::class)->reject($shelter);

        session()->flash('status', "“{$shelter->name}” barınağı reddedildi.");
    }

    public function render(): View
    {
        return view('livewire.shelter.shelter-approvals', [
            'shelters' => Shelter::with('admin')
                ->where('status', ShelterStatus::Pending)
                ->orderBy('created_at')
                ->get(),
        ]);
    }
}
