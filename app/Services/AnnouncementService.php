<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Announcement;
use App\Models\Donation;
use App\Models\Shelter;
use App\Models\User;
use App\Notifications\ShelterAnnouncementNotification;
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
