<?php

declare(strict_types=1);

namespace App\Livewire\Superadmin;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.panel')]
#[Title('Kullanıcılar — Re·Life')]
class UserList extends Component
{
    public string $search = '';

    public function ban(int $userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->id === auth()->id()) {
            session()->flash('status', 'Kendi hesabınızı banlayamazsınız.');

            return;
        }

        $user->update(['is_banned' => true]);

        session()->flash('status', "“{$user->name}” banlandı.");
    }

    public function unban(int $userId): void
    {
        $user = User::findOrFail($userId);

        $user->update(['is_banned' => false]);

        session()->flash('status', "“{$user->name}” kullanıcısının banı kaldırıldı.");
    }

    public function render(): View
    {
        $query = User::query();

        if (trim($this->search) !== '') {
            $term = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        return view('livewire.superadmin.user-list', [
            'users' => $query->orderBy('name')->get(),
            'currentUserId' => auth()->id(),
        ]);
    }
}
