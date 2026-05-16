<?php

declare(strict_types=1);

namespace App\Listeners\Donation;

use App\Actions\Donation\CreateCertificateAction;
use App\Events\Donation\DonationCreated;

/**
 * Bağış oluşturulduğunda bağışçı için teşekkür belgesi üretir.
 */
class IssueCertificateListener
{
    public function __construct(private CreateCertificateAction $createCertificate) {}

    public function handle(DonationCreated $event): void
    {
        // Bağışçısı olmayan (sistem) kayıtlar için belge üretilmez.
        if ($event->donation->user_id === null) {
            return;
        }

        $this->createCertificate->execute($event->donation);
    }
}
