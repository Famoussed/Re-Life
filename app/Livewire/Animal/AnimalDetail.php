<?php

declare(strict_types=1);

namespace App\Livewire\Animal;

use App\Enums\Shelter\ShelterStatus;
use App\Models\Animal\Animal;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AnimalDetail extends Component
{
    public Animal $animal;

    public function mount(Animal $animal): void
    {
        abort_unless(
            $animal->is_active && $animal->shelter->status === ShelterStatus::Approved,
            404
        );

        $this->animal = $animal->load([
            'shelter',
            'needs' => fn ($q) => $q->latest(),
            'recoveryUpdates' => fn ($q) => $q->with('images')->latest(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.animal.animal-detail')
            ->title($this->animal->name.' — Re·Life');
    }
}
