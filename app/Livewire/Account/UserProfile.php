<?php

declare(strict_types=1);

namespace App\Livewire\Account;

use App\Models\Account\User;
use App\Models\Donation\Certificate;
use App\Models\Donation\Donation;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class UserProfile extends Component
{
    public User $user;

    public function mount(User $user): void
    {
        abort_if($user->is_banned && ! (auth()->id() === $user->id), 404);

        $this->user = $user;
    }

    public function render(): View
    {
        $donations = Donation::where('user_id', $this->user->id)
            ->with(['animal', 'shelter'])
            ->latest()
            ->take(30)
            ->get();

        $supportedAnimals = $donations
            ->pluck('animal')
            ->filter()
            ->unique('id')
            ->take(8);

        return view('livewire.account.user-profile', [
            'donations' => $donations,
            'supportedAnimals' => $supportedAnimals,
            'donationCount' => Donation::where('user_id', $this->user->id)->count(),
            'badge' => $this->user->badge(),
            'isOwner' => auth()->id() === $this->user->id,
            'certificates' => Certificate::where('user_id', $this->user->id)
                ->latest('issued_at')
                ->take(30)
                ->get(),
        ])->title($this->user->name.' — Re·Life');
    }
}
