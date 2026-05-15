<?php

declare(strict_types=1);

namespace App\Livewire\Superadmin;

use App\Enums\Role;
use App\Enums\ShelterStatus;
use App\Models\Animal;
use App\Models\Donation;
use App\Models\Shelter;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.panel')]
#[Title('Platform Genel Bakışı — Re·Life')]
class Dashboard extends Component
{
    public function render(): View
    {
        $topShelterRows = Donation::query()
            ->selectRaw('shelter_id, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('shelter_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $shelters = Shelter::whereIn('id', $topShelterRows->pluck('shelter_id'))
            ->get()
            ->keyBy('id');

        return view('livewire.superadmin.dashboard', [
            'totalShelters' => Shelter::count(),
            'approvedShelters' => Shelter::where('status', ShelterStatus::Approved)->count(),
            'pendingShelters' => Shelter::where('status', ShelterStatus::Pending)->count(),
            'totalUsers' => User::where('role', Role::User)->count(),
            'totalAnimals' => Animal::count(),
            'totalDonationAmount' => (float) Donation::sum('amount'),
            'totalDonationCount' => Donation::count(),
            'topShelters' => $topShelterRows
                ->map(fn ($row) => [
                    'shelter' => $shelters->get($row->shelter_id),
                    'total' => (float) $row->total,
                    'count' => (int) $row->count,
                ])
                ->filter(fn ($row) => $row['shelter'] !== null)
                ->values(),
        ]);
    }
}
