<?php

declare(strict_types=1);

namespace App\Livewire\Animal;

use App\Enums\Shelter\ShelterStatus;
use App\Models\Animal\Animal;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dostluk Albümü — Re·Life')]
class AnimalList extends Component
{
    #[Url]
    public string $species = '';

    #[Url]
    public string $city = '';

    #[Url]
    public string $needType = '';

    public function updated(): void
    {
        // Filtre değişiminde sayfa başına dön (gerekirse).
    }

    public function resetFilters(): void
    {
        $this->reset(['species', 'city', 'needType']);
    }

    public function render(): View
    {
        $query = Animal::query()
            ->where('is_active', true)
            ->whereHas('shelter', fn ($q) => $q->where('status', ShelterStatus::Approved->value))
            ->with(['shelter', 'activeNeeds'])
            ->latest();

        if ($this->species !== '') {
            $query->where('species', $this->species);
        }

        if ($this->city !== '') {
            $query->whereHas('shelter', fn ($q) => $q->where('city', $this->city));
        }

        if ($this->needType !== '') {
            $query->whereHas('needs', fn ($q) => $q->where('type', $this->needType)->where('status', 'active'));
        }

        $cities = \App\Models\Shelter\Shelter::where('status', ShelterStatus::Approved->value)
            ->orderBy('city')
            ->pluck('city')
            ->unique()
            ->values();

        return view('livewire.animal.animal-list', [
            'animals' => $query->get(),
            'cities' => $cities,
            'totalActive' => Animal::where('is_active', true)->count(),
        ]);
    }
}
