<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Donation;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.panel')]
#[Title('Bağışlar — Re·Life')]
class DonationList extends Component
{
    #[Url]
    public string $period = '';

    public function resetFilters(): void
    {
        $this->reset('period');
    }

    public function render(): View
    {
        $shelter = auth()->user()->shelter;

        $query = Donation::where('shelter_id', $shelter->id)
            ->with(['user', 'animal'])
            ->latest('created_at');

        if ($this->period === 'month') {
            $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);
        } elseif ($this->period === 'year') {
            $query->whereYear('created_at', now()->year);
        }

        $donations = $query->get();

        return view('livewire.admin.donation-list', [
            'donations' => $donations,
            'total' => (float) $donations->sum('amount'),
            'count' => $donations->count(),
        ]);
    }
}
