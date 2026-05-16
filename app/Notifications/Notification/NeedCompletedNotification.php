<?php

declare(strict_types=1);

namespace App\Notifications\Notification;

use App\Models\Animal\Need;
use Illuminate\Notifications\Notification;

class NeedCompletedNotification extends Notification
{
    public function __construct(public Need $need)
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
            'type' => 'need_completed',
            'title' => 'Bir ihtiyaç tamamlandı! 🎉',
            'message' => "{$this->need->animal->name} için \"{$this->need->title}\" ihtiyacı hedefe ulaştı. Katkınla mümkün oldu.",
            'url' => route('animals.show', $this->need->animal_id),
        ];
    }
}
