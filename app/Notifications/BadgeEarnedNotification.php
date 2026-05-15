<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Badge;
use Illuminate\Notifications\Notification;

class BadgeEarnedNotification extends Notification
{
    public function __construct(public Badge $badge)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'badge_earned',
            'title' => 'Yeni bir rozet kazandın! 🏅',
            'message' => "\"{$this->badge->name}\" rozetine ulaştın. İyiliğin için teşekkürler.",
            'url' => route('users.show', $notifiable->id),
        ];
    }
}
