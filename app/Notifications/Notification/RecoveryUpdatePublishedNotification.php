<?php

declare(strict_types=1);

namespace App\Notifications\Notification;

use App\Models\Animal\RecoveryUpdate;
use Illuminate\Notifications\Notification;

class RecoveryUpdatePublishedNotification extends Notification
{
    public function __construct(public RecoveryUpdate $update) {}

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
            'type' => 'recovery_update',
            'title' => "{$this->update->animal->name} için iyileşme haberi 🌱",
            'message' => $this->update->title,
            'url' => route('animals.show', $this->update->animal_id).'#iyilesme',
        ];
    }
}
