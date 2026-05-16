<?php

declare(strict_types=1);

namespace App\Listeners\Donation;

use App\Actions\Animal\ApplyNeedProgressAction;
use App\Events\Donation\DonationCreated;
use App\Models\Donation\Donation;
use App\Models\Animal\Need;
use App\Models\Account\User;
use App\Notifications\Notification\NeedCompletedNotification;
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
