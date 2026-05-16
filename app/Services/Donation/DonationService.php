<?php

declare(strict_types=1);

namespace App\Services\Donation;

use App\Actions\Donation\CreateDonationAction;
use App\Events\Donation\DonationCreated;
use App\Models\Account\User;
use App\Models\Animal\Animal;
use App\Models\Animal\Need;
use App\Models\Donation\Donation;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DonationService
{
    public function __construct(private CreateDonationAction $createDonation) {}

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

    /**
     * Bir hayvanın tüm aktif ihtiyaçlarını tek seferde karşılar:
     * her ihtiyaç için kalan tutarı kadar ayrı bir bağış oluşturulur.
     * Toplam bağışlanan tutarı döndürür.
     *
     * @param  array<string, mixed>  $payment  is_anonymous, card_number, card_holder
     */
    public function coverAllNeeds(User $donor, Animal $animal, array $payment): float
    {
        $needs = $animal->activeNeeds()->withoutGlobalScopes()->get();

        if ($needs->isEmpty()) {
            throw new RuntimeException('Bu dostun şu an karşılanacak aktif bir ihtiyacı yok.');
        }

        return DB::transaction(function () use ($donor, $needs, $payment): float {
            $total = 0.0;

            foreach ($needs as $need) {
                $remaining = $need->remainingAmount();

                if ($remaining <= 0) {
                    continue;
                }

                $donation = $this->createDonation->execute($donor, [
                    'shelter_id' => $need->shelter_id,
                    'animal_id' => $need->animal_id,
                    'need_id' => $need->id,
                    'amount' => $remaining,
                    'is_anonymous' => $payment['is_anonymous'] ?? false,
                    'card_number' => $payment['card_number'] ?? null,
                    'card_holder' => $payment['card_holder'] ?? null,
                ]);

                DonationCreated::dispatch($donation);
                $total += $remaining;
            }

            if ($total <= 0) {
                throw new RuntimeException('Bu dostun ihtiyaçları zaten karşılanmış.');
            }

            return $total;
        });
    }
}
