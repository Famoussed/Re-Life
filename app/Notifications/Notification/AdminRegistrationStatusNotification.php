<?php

declare(strict_types=1);

namespace App\Notifications\Notification;

use App\Models\Shelter\Shelter;
use Illuminate\Notifications\Notification;

class AdminRegistrationStatusNotification extends Notification
{
    public function __construct(public Shelter $shelter, public bool $approved)
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
            'type' => 'admin_registration_status',
            'title' => $this->approved ? 'Barınağınız onaylandı! 🎉' : 'Barınak başvurunuz reddedildi',
            'message' => $this->approved
                ? "{$this->shelter->name} artık yayında. Hayvanlarınızı ve ihtiyaçlarınızı ekleyebilirsiniz."
                : "{$this->shelter->name} başvurusu onaylanmadı. Lütfen bizimle iletişime geçin.",
            'url' => $this->approved ? route('admin.dashboard') : route('home'),
        ];
    }
}
