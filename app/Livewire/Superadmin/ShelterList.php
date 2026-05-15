<?php

declare(strict_types=1);

namespace App\Livewire\Superadmin;

use App\Enums\ShelterStatus;
use App\Models\Shelter;
use App\Services\ShelterService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.panel')]
#[Title('Tüm Barınaklar — Re·Life')]
class ShelterList extends Component
{
    public string $statusFilter = '';

    public function suspend(int $shelterId): void
    {
        $shelter = Shelter::where('status', ShelterStatus::Approved)->findOrFail($shelterId);

        app(ShelterService::class)->suspend($shelter);

        session()->flash('status', "“{$shelter->name}” barınağı askıya alındı.");
    }

    public function activate(int $shelterId): void
    {
        $shelter = Shelter::where('status', ShelterStatus::Suspended)->findOrFail($shelterId);

        app(ShelterService::class)->activate($shelter);

        session()->flash('status', "“{$shelter->name}” barınağı yeniden aktive edildi.");
    }

    public function render(): View
    {
        $query = Shelter::with('admin')->withCount('animals');

        if ($this->statusFilter !== '' && ShelterStatus::tryFrom($this->statusFilter) !== null) {
            $query->where('status', $this->statusFilter);
        }

        return view('livewire.superadmin.shelter-list', [
            'shelters' => $query->orderBy('name')->get(),
            'statuses' => ShelterStatus::cases(),
        ]);
    }
}
