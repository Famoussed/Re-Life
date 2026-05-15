<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Notifications\Notification;

class ShelterAnnouncementNotification extends Notification
{
    public function __construct(public Announcement $announcement)
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
            'type' => 'shelter_announcement',
            'title' => "{$this->announcement->shelter->name} duyuru paylaştı",
            'message' => $this->announcement->title,
            'url' => route('shelters.show', $this->announcement->shelter_id),
        ];
    }
}
