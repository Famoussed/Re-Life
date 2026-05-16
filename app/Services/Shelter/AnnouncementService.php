<?php

declare(strict_types=1);

namespace App\Services\Shelter;

use App\Models\Shelter\Announcement;
use App\Models\Donation\Donation;
use App\Models\Shelter\Shelter;
use App\Models\Account\User;
use App\Notifications\Notification\ShelterAnnouncementNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class AnnouncementService
{
    /**
     * Duyuru yayınlar ve barınağın bağışçılarına bildirim gönderir.
     *
     * @param  array<string, mixed>  $data
     */
    public function publish(Shelter $shelter, array $data): Announcement
    {
        return DB::transaction(function () use ($shelter, $data) {
            $announcement = Announcement::create([
                'shelter_id' => $shelter->id,
                'title' => $data['title'],
                'body' => $data['body'],
            ]);

            $donorIds = Donation::where('shelter_id', $shelter->id)
                ->distinct()
                ->pluck('user_id');

            $donors = User::whereIn('id', $donorIds)->get();

            Notification::send($donors, new ShelterAnnouncementNotification($announcement));

            return $announcement;
        });
    }
}
