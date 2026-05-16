<?php

declare(strict_types=1);

namespace App\Livewire\Shelter;

use App\Models\Animal\Animal;
use App\Models\Donation\Donation;
use App\Models\Animal\Need;
use App\Models\Account\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.panel')]
#[Title('Genel Bakış — Re·Life')]
class AdminDashboard extends Component
{
    public function render(): View
    {
        $shelter = auth()->user()->shelter;

        $monthTotal = Donation::where('shelter_id', $shelter->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $yearTotal = Donation::where('shelter_id', $shelter->id)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $allTimeTotal = Donation::where('shelter_id', $shelter->id)->sum('amount');

        $recentDonations = Donation::where('shelter_id', $shelter->id)
            ->with('user')
            ->latest('created_at')
            ->limit(10)
            ->get();

        $topDonorRows = Donation::where('shelter_id', $shelter->id)
            ->selectRaw('user_id, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $users = User::whereIn('id', $topDonorRows->pluck('user_id'))->get()->keyBy('id');

        return view('livewire.shelter.admin-dashboard', [
            'shelter' => $shelter,
            'monthTotal' => (float) $monthTotal,
            'yearTotal' => (float) $yearTotal,
            'allTimeTotal' => (float) $allTimeTotal,
            'activeAnimals' => Animal::where('is_active', true)->count(),
            'activeNeeds' => Need::where('status', 'active')->count(),
            'recentDonations' => $recentDonations,
            'topDonors' => $topDonorRows->map(fn ($row) => [
                'user' => $users->get($row->user_id),
                'total' => (float) $row->total,
                'count' => (int) $row->count,
            ]),
        ]);
    }
}
