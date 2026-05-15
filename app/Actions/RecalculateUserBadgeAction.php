<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Badge;
use App\Models\Donation;
use App\Models\User;

/**
 * Kullanıcının global toplam bağışını ve rozet seviyesini yeniden hesaplar.
 * Önceki rozet seviyesini döner (seviye atlama tespiti için).
 */
class RecalculateUserBadgeAction
{
    public function execute(User $user): int
    {
        $previousLevel = (int) $user->badge_level;

        $total = (float) Donation::where('user_id', $user->id)->sum('amount');

        $level = (int) (Badge::where('min_amount', '<=', $total)->max('level') ?? 0);

        $user->total_donated = $total;
        $user->badge_level = $level;
        $user->save();

        return $previousLevel;
    }
}
