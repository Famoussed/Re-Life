<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\RecalculateUserBadgeAction;
use App\Events\DonationCreated;
use App\Models\Badge;
use App\Notifications\BadgeEarnedNotification;

class UpdateUserBadgeListener
{
    public function __construct(private RecalculateUserBadgeAction $recalculate)
    {
    }

    public function handle(DonationCreated $event): void
    {
        $user = $event->donation->user;

        if ($user === null) {
            return;
        }

        $previousLevel = $this->recalculate->execute($user);

        if ($user->badge_level > $previousLevel) {
            $badge = Badge::where('level', $user->badge_level)->first();

            if ($badge !== null) {
                $user->notify(new BadgeEarnedNotification($badge));
            }
        }
    }
}
