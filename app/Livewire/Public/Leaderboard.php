<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Enums\Role;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Sıralama — Re·Life')]
class Leaderboard extends Component
{
    /** all | year | month */
    #[Url]
    public string $tab = 'all';

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['all', 'year', 'month'], true) ? $tab : 'all';
    }

    public function render(): View
    {
        $rows = $this->tab === 'all' ? $this->allTimeRows() : $this->periodRows();

        return view('livewire.public.leaderboard', ['rows' => $rows]);
    }

    /**
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function allTimeRows(): \Illuminate\Support\Collection
    {
        return User::where('role', Role::User->value)
            ->where('is_banned', false)
            ->where('total_donated', '>', 0)
            ->orderByDesc('total_donated')
            ->take(100)
            ->get()
            ->map(fn (User $u) => [
                'user' => $u,
                'amount' => (float) $u->total_donated,
                'badge_level' => (int) $u->badge_level,
                'anonymous' => false,
            ]);
    }

    /**
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function periodRows(): \Illuminate\Support\Collection
    {
        $query = Donation::query()
            ->selectRaw('user_id, SUM(amount) as total, MIN(is_anonymous) as all_anon')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->take(100);

        if ($this->tab === 'year') {
            $query->whereYear('created_at', Carbon::now()->year);
        } else {
            $query->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month);
        }

        $aggregates = $query->get();
        $users = User::whereIn('id', $aggregates->pluck('user_id'))->get()->keyBy('id');

        return $aggregates
            ->filter(fn ($row) => isset($users[$row->user_id]) && ! $users[$row->user_id]->is_banned)
            ->map(fn ($row) => [
                'user' => $users[$row->user_id],
                'amount' => (float) $row->total,
                'badge_level' => (int) $users[$row->user_id]->badge_level,
                // Dönemdeki tüm bağışları anonimse isim gizlenir.
                'anonymous' => (bool) $row->all_anon,
            ])
            ->values();
    }
}
