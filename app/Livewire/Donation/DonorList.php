<?php

declare(strict_types=1);

namespace App\Livewire\Donation;

use App\Models\Donation\Donation;
use App\Models\Account\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.panel')]
#[Title('Bağışçılar — Re·Life')]
class DonorList extends Component
{
    public function render(): View
    {
        $shelter = auth()->user()->shelter;

        $rows = Donation::where('shelter_id', $shelter->id)
            ->whereNotNull('user_id')
            ->selectRaw('user_id, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->get();

        $users = User::whereIn('id', $rows->pluck('user_id'))->get()->keyBy('id');

        $donors = $rows
            ->map(fn ($row) => [
                'user' => $users->get($row->user_id),
                'total' => (float) $row->total,
                'count' => (int) $row->count,
            ])
            ->filter(fn ($d) => $d['user'] !== null)
            ->values();

        return view('livewire.donation.donor-list', [
            'donors' => $donors,
        ]);
    }
}
