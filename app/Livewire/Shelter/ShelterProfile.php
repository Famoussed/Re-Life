<?php

declare(strict_types=1);

namespace App\Livewire\Shelter;

use App\Enums\Shelter\ShelterStatus;
use App\Models\Shelter\Shelter;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ShelterProfile extends Component
{
    public Shelter $shelter;

    public function mount(Shelter $shelter): void
    {
        abort_unless($shelter->status === ShelterStatus::Approved, 404);

        $this->shelter = $shelter;
    }

    public function render(): View
    {
        $animals = $this->shelter->animals()
            ->where('is_active', true)
            ->with(['shelter', 'activeNeeds'])
            ->latest()
            ->get();

        $announcements = $this->shelter->announcements()->latest()->take(5)->get();

        return view('livewire.shelter.shelter-profile', [
            'animals' => $animals,
            'announcements' => $announcements,
            'totalRaised' => (float) $this->shelter->donations()->sum('amount'),
        ])->title($this->shelter->name.' — Re·Life');
    }
}
