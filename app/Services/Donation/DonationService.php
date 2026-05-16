<?php

declare(strict_types=1);

namespace App\Services\Donation;

use App\Actions\Donation\CreateDonationAction;
use App\Events\Donation\DonationCreated;
use App\Models\Donation\Donation;
use App\Models\Animal\Need;
use App\Models\Account\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DonationService
{
    public function __construct(private CreateDonationAction $createDonation)
    {
    }

    /**
     * Bir bağış oluşturur ve DonationCreated event'ini tetikler.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(User $donor, array $data): Donation
    {
        if ((float) ($data['amount'] ?? 0) <= 0) {
            throw new RuntimeException('Bağış tutarı sıfırdan büyük olmalıdır.');
        }

        // Spesifik bağışta ihtiyaç aktif olmalı ve hayvan/barınakla tutarlı olmalı.
        if (! empty($data['need_id'])) {
            $need = Need::withoutGlobalScopes()->find($data['need_id']);

            if ($need === null || ! $need->isActive()) {
                throw new RuntimeException('Bu ihtiyaç artık bağışa açık değil.');
            }

            $data['animal_id'] = $need->animal_id;
            $data['shelter_id'] = $need->shelter_id;
        }

        return DB::transaction(function () use ($donor, $data) {
            $donation = $this->createDonation->execute($donor, $data);

            DonationCreated::dispatch($donation);

            return $donation;
        });
    }
}
