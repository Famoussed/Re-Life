<?php

declare(strict_types=1);

namespace App\Actions\Donation;

use App\Models\Donation\Certificate;
use App\Models\Donation\Donation;

/**
 * Bir bağış için teşekkür belgesi üretir. Bağış başına yalnızca bir belge
 * oluşturulur (idempotent); belge numarası kayıt id'sinden türetilir.
 */
class CreateCertificateAction
{
    public function execute(Donation $donation): Certificate
    {
        $certificate = Certificate::firstOrCreate(
            ['donation_id' => $donation->id],
            [
                'user_id' => $donation->user_id,
                'certificate_no' => 'GECICI',
                'donor_name' => $donation->user?->name ?? 'Bağışçı',
                'animal_name' => $donation->animal?->name,
                'amount' => $donation->amount,
                'issued_at' => $donation->created_at ?? now(),
            ]
        );

        // Yeni kayıtsa okunabilir, sıralı belge numarasını id'den türet.
        if ($certificate->wasRecentlyCreated) {
            $certificate->update([
                'certificate_no' => sprintf(
                    'RL-%d-%s',
                    ($donation->created_at ?? now())->year,
                    str_pad((string) $certificate->id, 6, '0', STR_PAD_LEFT)
                ),
            ]);
        }

        return $certificate;
    }
}
