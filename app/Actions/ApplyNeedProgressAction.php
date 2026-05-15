<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\NeedStatus;
use App\Models\Need;
use Illuminate\Support\Carbon;

/**
 * Bir ihtiyacın toplanan tutarını artırır; hedefe ulaşıldıysa tamamlandı yapar.
 * Tamamlanma anında true döner.
 */
class ApplyNeedProgressAction
{
    public function execute(Need $need, float $amount): bool
    {
        $need->collected_amount = (float) $need->collected_amount + $amount;
        $justCompleted = false;

        if ($need->status === NeedStatus::Active
            && (float) $need->collected_amount >= (float) $need->target_amount) {
            $need->status = NeedStatus::Completed;
            $need->completed_at = Carbon::now();
            $justCompleted = true;
        }

        $need->save();

        return $justCompleted;
    }
}
