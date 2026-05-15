<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\ApplyNeedProgressAction;
use App\Events\DonationCreated;
use App\Models\Donation;
use App\Models\Need;
use App\Models\User;
use App\Notifications\NeedCompletedNotification;
use Illuminate\Support\Facades\Notification;

class UpdateNeedProgressListener
{
    public function __construct(private ApplyNeedProgressAction $applyProgress)
    {
    }

    public function handle(DonationCreated $event): void
    {
        $donation = $event->donation;

        if ($donation->need_id === null) {
            return;
        }

        $need = Need::withoutGlobalScopes()->with('animal')->find($donation->need_id);

        if ($need === null) {
            return;
        }

        $justCompleted = $this->applyProgress->execute($need, (float) $donation->amount);

        if ($justCompleted) {
            $supporterIds = Donation::where('need_id', $need->id)
                ->distinct()
                ->pluck('user_id');

            $supporters = User::whereIn('id', $supporterIds)->get();

            Notification::send($supporters, new NeedCompletedNotification($need));
        }
    }
}
