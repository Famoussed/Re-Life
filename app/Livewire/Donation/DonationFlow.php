<?php

declare(strict_types=1);

namespace App\Livewire\Donation;

use App\Enums\Shelter\ShelterStatus;
use App\Models\Animal\Animal;
use App\Models\Animal\Need;
use App\Models\Shelter\Shelter;
use App\Services\Donation\DonationService;
use Carbon\Carbon;
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

    #[Url]
    public ?int $animal = null;

    public ?Need $selectedNeed = null;

    public ?Shelter $selectedShelter = null;

    public ?Animal $selectedAnimal = null;

    /**
     * "Tüm masrafları öde" modu: hayvanın tüm aktif ihtiyaçları tek seferde karşılanır.
     */
    public bool $coverAllMode = false;

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

        // "Tüm masrafları öde" — hayvanın aktif ihtiyaçlarının kalan toplamı.
        if ($this->selectedNeed === null && $this->animal !== null) {
            $this->selectedAnimal = Animal::withoutGlobalScopes()
                ->with(['shelter', 'activeNeeds'])
                ->find($this->animal);

            if ($this->selectedAnimal && $this->selectedAnimal->activeNeeds->isNotEmpty()) {
                $this->selectedShelter = $this->selectedAnimal->shelter;
                $this->coverAllMode = true;
                $this->amount = $this->totalRemaining();
                $this->step = 2; // Tutar sabit — doğrudan ödemeye geç.
            } else {
                $this->selectedAnimal = null;
            }
        }

        if ($this->selectedNeed === null && $this->selectedAnimal === null && $this->shelter !== null) {
            $this->selectedShelter = Shelter::where('status', ShelterStatus::Approved->value)
                ->find($this->shelter);
        }
    }

    /**
     * "Tüm masrafları öde" modunda hayvanın aktif ihtiyaçlarının kalan toplamı.
     */
    public function totalRemaining(): float
    {
        if ($this->selectedAnimal === null) {
            return 0.0;
        }

        return (float) $this->selectedAnimal->activeNeeds
            ->sum(fn (Need $need): float => $need->remainingAmount());
    }

    /**
     * Seçili ihtiyaç bağışı için izin verilen en yüksek tutar (kalan tutar).
     * Spesifik ihtiyaç seçili değilse sınır yoktur (null).
     */
    public function maxAmount(): ?float
    {
        return $this->selectedNeed?->remainingAmount();
    }

    public function pickAmount(int $value): void
    {
        $this->amount = $this->capAmount((float) $value);
        $this->customAmount = '';
        $this->resetErrorBag('amount');
    }

    public function updatedCustomAmount(string $value): void
    {
        $entered = (float) max(0, (int) preg_replace('/\D/', '', $value));
        $capped = $this->capAmount($entered);

        $this->amount = $capped;

        // Tutar kalan ihtiyacın üzerindeyse otomatik kıs ve kullanıcıyı bilgilendir.
        if ($capped < $entered) {
            $this->customAmount = (string) (int) $capped;
            $this->addError('amount', sprintf(
                'Bu ihtiyaç için en fazla ₺%s bağışlayabilirsin — kalan tutar bu kadar.',
                number_format($capped, 0, ',', '.')
            ));
        } else {
            $this->resetErrorBag('amount');
        }
    }

    /**
     * Tutarı, seçili ihtiyacın kalan miktarıyla sınırlar.
     * Spesifik ihtiyaç yoksa tutar olduğu gibi döner.
     */
    private function capAmount(float $value): float
    {
        $max = $this->maxAmount();

        return $max === null ? $value : min($value, $max);
    }

    public function goToPayment(): void
    {
        $this->validate([
            'amount' => 'required|numeric|min:1',
        ], [], ['amount' => 'tutar']);

        if ($this->selectedNeed === null && $this->selectedShelter === null && $this->selectedAnimal === null) {
            $this->addError('amount', 'Lütfen bir hayvan ihtiyacı veya barınak seç.');

            return;
        }

        $this->step = 2;
    }

    /**
     * Kart numarası kuralı: 13-19 hane olmalı ve Luhn algoritmasından geçmeli.
     * Boşluklar yok sayılır; rakam dışı karakter geçersizdir.
     */
    private function cardNumberRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            $raw = (string) $value;

            // Yalnızca rakam ve boşluk kabul edilir.
            if (preg_match('/[^\d\s]/', $raw)) {
                $fail('Kart numarası yalnızca rakamlardan oluşmalıdır.');

                return;
            }

            $digits = preg_replace('/\s+/', '', $raw);

            if (strlen($digits) < 13 || strlen($digits) > 19) {
                $fail('Geçerli bir kart numarası giriniz (13-19 hane).');

                return;
            }

            if (! $this->passesLuhn($digits)) {
                $fail('Girilen kart numarası geçerli değil.');
            }
        };
    }

    /**
     * Son kullanma kuralı: AA/YY biçiminde olmalı, ay 01-12 aralığında
     * ve tarih içinde bulunulan aydan ileride olmalıdır.
     */
    private function cardExpiryRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if (! preg_match('/^(0[1-9]|1[0-2])\s*\/\s*(\d{2})$/', (string) $value, $m)) {
                $fail('Son kullanma tarihi AA/YY biçiminde olmalıdır.');

                return;
            }

            $month = (int) $m[1];
            $year = 2000 + (int) $m[2];

            // Ayın son gününü temsil eden tarih ile karşılaştır.
            $expiry = Carbon::createFromDate($year, $month, 1)->endOfMonth();

            if ($expiry->isPast()) {
                $fail('Kartın son kullanma tarihi geçmiş.');
            }
        };
    }

    /**
     * Luhn (mod 10) sağlama algoritması.
     */
    private function passesLuhn(string $digits): bool
    {
        $sum = 0;
        $alternate = false;

        for ($i = strlen($digits) - 1; $i >= 0; $i--) {
            $n = (int) $digits[$i];

            if ($alternate) {
                $n *= 2;

                if ($n > 9) {
                    $n -= 9;
                }
            }

            $sum += $n;
            $alternate = ! $alternate;
        }

        return $sum % 10 === 0;
    }

    public function donate(DonationService $donations): void
    {
        $this->validate([
            'amount' => 'required|numeric|min:1',
            // Kart sahibi: yalnızca harf ve boşluk (Türkçe karakterler dahil), 3-60 karakter.
            'cardHolder' => ['required', 'string', 'min:3', 'max:60', 'regex:/^[\p{L}\s\.\'-]+$/u'],
            // Kart numarası: 13-19 hane (boşluklar yok sayılır) + Luhn kontrolü.
            'cardNumber' => ['required', 'string', $this->cardNumberRule()],
            // Son kullanma: AA/YY biçimi + tarihin gelecekte olması.
            'cardExpiry' => ['required', 'string', $this->cardExpiryRule()],
            // CVV: yalnızca 3 veya 4 hane.
            'cardCvv' => ['required', 'string', 'regex:/^\d{3,4}$/'],
        ], [
            'cardHolder.regex' => 'Kart sahibi adı yalnızca harflerden oluşmalıdır.',
            'cardCvv.regex' => 'Güvenlik kodu 3 veya 4 haneli olmalıdır.',
        ], [
            'cardHolder' => 'kart sahibi',
            'cardNumber' => 'kart numarası',
            'cardExpiry' => 'son kullanma',
            'cardCvv' => 'güvenlik kodu',
        ]);

        try {
            if ($this->coverAllMode && $this->selectedAnimal !== null) {
                // Tüm masraflar: her aktif ihtiyaca kalan tutarı kadar ayrı bağış.
                $this->amount = $donations->coverAllNeeds(auth()->user(), $this->selectedAnimal, [
                    'is_anonymous' => $this->isAnonymous,
                    'card_number' => $this->cardNumber,
                    'card_holder' => $this->cardHolder,
                ]);
            } else {
                $donations->create(auth()->user(), [
                    'shelter_id' => $this->selectedShelter?->id,
                    'need_id' => $this->selectedNeed?->id,
                    'amount' => $this->amount,
                    'is_anonymous' => $this->isAnonymous,
                    'card_number' => $this->cardNumber,
                    'card_holder' => $this->cardHolder,
                ]);
            }
        } catch (RuntimeException $e) {
            $this->addError('amount', $e->getMessage());
            $this->step = $this->coverAllMode ? 2 : 1;

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
