<?php

declare(strict_types=1);

namespace App\Actions\Donation;

use App\Models\Donation\Donation;
use App\Models\Account\User;
use Illuminate\Support\Carbon;

/**
 * Tek bir bağış kaydı oluşturur. Kart verisinden yalnızca son 4 hane saklanır.
 */
class CreateDonationAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(User $donor, array $data): Donation
    {
        $cardNumber = preg_replace('/\D/', '', (string) ($data['card_number'] ?? ''));
        $last4 = $cardNumber !== '' ? substr($cardNumber, -4) : '----';

        return Donation::create([
            'user_id' => $donor->id,
            'shelter_id' => $data['shelter_id'],
            'animal_id' => $data['animal_id'] ?? null,
            'need_id' => $data['need_id'] ?? null,
            'amount' => $data['amount'],
            'currency' => 'TRY',
            'is_anonymous' => (bool) ($data['is_anonymous'] ?? false),
            'payment_meta' => [
                'card_last4' => $last4,
                'card_holder' => $data['card_holder'] ?? null,
            ],
            'created_at' => Carbon::now(),
        ]);
    }
}
