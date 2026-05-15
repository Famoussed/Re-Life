<?php

declare(strict_types=1);

namespace App\Livewire\Donation;

use App\Enums\ShelterStatus;
use App\Models\Need;
use App\Models\Shelter;
use App\Services\DonationService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use RuntimeException;

#[Layout('layouts.app')]
#[Title('Bağış Yap — Re·Life')]
class DonationFlow extends Component
{
    #[Url]
    public ?int $need = null;

    #[Url]
    public ?int $shelter = null;

    public ?Need $selectedNeed = null;

    public ?Shelter $selectedShelter = null;

    public int $step = 1;

    public float $amount = 0;

    public string $customAmount = '';

    public bool $isAnonymous = false;

    public string $cardHolder = '';

    public string $cardNumber = '';

    public string $cardExpiry = '';

    public string $cardCvv = '';

    public bool $done = false;

    public function mount(): void
    {
        if ($this->need !== null) {
            $this->selectedNeed = Need::withoutGlobalScopes()
                ->with(['animal', 'shelter'])
                ->find($this->need);

            if ($this->selectedNeed && $this->selectedNeed->isActive()) {
                $this->selectedShelter = $this->selectedNeed->shelter;
            } else {
                $this->selectedNeed = null;
            }
        }

        if ($this->selectedNeed === null && $this->shelter !== null) {
            $this->selectedShelter = Shelter::where('status', ShelterStatus::Approved->value)
                ->find($this->shelter);
        }
    }

    public function pickAmount(int $value): void
    {
        $this->amount = $value;
        $this->customAmount = '';
    }

    public function updatedCustomAmount(string $value): void
    {
        $this->amount = (float) max(0, (int) preg_replace('/\D/', '', $value));
    }

    public function goToPayment(): void
    {
        $this->validate([
            'amount' => 'required|numeric|min:1',
        ], [], ['amount' => 'tutar']);

        if ($this->selectedNeed === null && $this->selectedShelter === null) {
            $this->addError('amount', 'Lütfen bir hayvan ihtiyacı veya barınak seç.');

            return;
        }

        $this->step = 2;
    }

    public function donate(DonationService $donations): void
    {
        $this->validate([
            'amount' => 'required|numeric|min:1',
            'cardHolder' => 'required|string|min:3',
            'cardNumber' => 'required|string|min:12',
            'cardExpiry' => 'required|string|min:4',
            'cardCvv' => 'required|string|min:3',
        ], [], [
            'cardHolder' => 'kart sahibi',
            'cardNumber' => 'kart numarası',
            'cardExpiry' => 'son kullanma',
            'cardCvv' => 'güvenlik kodu',
        ]);

        try {
            $donations->create(auth()->user(), [
                'shelter_id' => $this->selectedShelter?->id,
                'need_id' => $this->selectedNeed?->id,
                'amount' => $this->amount,
                'is_anonymous' => $this->isAnonymous,
                'card_number' => $this->cardNumber,
                'card_holder' => $this->cardHolder,
            ]);
        } catch (RuntimeException $e) {
            $this->addError('amount', $e->getMessage());
            $this->step = 1;

            return;
        }

        $this->done = true;
        $this->step = 3;
    }

    public function render(): View
    {
        return view('livewire.donation.donation-flow');
    }
}
