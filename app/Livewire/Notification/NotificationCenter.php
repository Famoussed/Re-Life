<?php

declare(strict_types=1);

namespace App\Livewire\Notification;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Bildirimler — Re·Life')]
class NotificationCenter extends Component
{
    public function markAsRead(string $id): void
    {
        auth()->user()->notifications()->where('id', $id)->first()?->markAsRead();
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function render(): View
    {
        return view('livewire.notification.notification-center', [
            'notifications' => auth()->user()->notifications()->latest()->take(50)->get(),
        ]);
    }
}
