<div class="max-w-[640px] mx-auto px-5 sm:px-8 py-10">

    @if($step === 3 && $done)
        {{-- BAŞARI --}}
        <div class="paper-card rounded-4xl p-8 shadow-card border border-cream-300/50 text-center">
            <div class="text-[52px]">🌿</div>
            <h1 class="font-modern text-[40px] text-ink-900 leading-tight mt-2">Bir adım daha atıldı</h1>
            <p class="text-[15px] text-ink-700/75 mt-2">
                ₺{{ number_format($amount, 0, ',', '.') }} bağışın kaydedildi. İyiliğin için teşekkürler —
                bu küçük an, biri için kocaman.
            </p>
            <div class="flex gap-3 justify-center mt-6">
                <a href="{{ route('home') }}" class="rounded-full bg-sage-600 hover:bg-sage-700 text-cream-50 px-6 py-3 text-[14px] font-medium">Başka dostlara bak</a>
                <a href="{{ route('users.show', auth()->user()) }}" class="rounded-full bg-cream-100 hover:bg-cream-200 border border-cream-300/60 text-ink-700 px-6 py-3 text-[14px]">Profilim</a>
            </div>
        </div>
    @else

    <a href="{{ route('home') }}" class="text-[13px] text-sage-700 hover:text-sage-600">← Vazgeç</a>
    <h1 class="font-modern text-[42px] text-ink-900 leading-tight mt-2">Bir adım hediye et</h1>

    {{-- Kapsam özeti --}}
    <div class="paper-note rounded-2xl p-4 shadow-note mt-4">
        @if($coverAllMode && $selectedAnimal)
            <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500">Tüm masrafları karşıla</div>
            <div class="font-modern text-[19px] text-ink-900 mt-0.5">{{ $selectedAnimal->name }} · tüm aktif ihtiyaçlar</div>
            <div class="text-[13px] text-ink-700/65">{{ $selectedShelter->name }} · {{ $selectedShelter->city }}</div>
            <ul class="mt-3 space-y-1.5 border-t border-clay-200/60 pt-3">
                @foreach($selectedAnimal->activeNeeds as $n)
                    <li class="flex items-baseline justify-between text-[13px]">
                        <span class="text-ink-700/80">{{ $n->title }}</span>
                        <span class="text-ink-900 font-semibold whitespace-nowrap">₺{{ number_format($n->remainingAmount(), 0, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>
        @elseif($selectedNeed)
            <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500">Spesifik ihtiyaç</div>
            <div class="font-modern text-[19px] text-ink-900 mt-0.5">{{ $selectedNeed->title }}</div>
            <div class="text-[13px] text-ink-700/65">{{ $selectedNeed->animal->name }} · {{ $selectedShelter->name }}</div>
        @elseif($selectedShelter)
            <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500">Barınağa genel destek</div>
            <div class="font-modern text-[19px] text-ink-900 mt-0.5">{{ $selectedShelter->name }}</div>
            <div class="text-[13px] text-ink-700/65">{{ $selectedShelter->city }}</div>
        @else
            <div class="text-[14px] text-clay-600">Bir hayvan sayfasından ihtiyaç seç ya da bir barınağa genel destek ol.</div>
        @endif
    </div>

    @if($step === 1)
        {{-- ADIM 1 — TUTAR --}}
        <div class="paper-card rounded-4xl p-6 sm:p-7 shadow-card border border-cream-300/50 mt-5">
            <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500 mb-3">Ne kadar destek olmak istersin?</div>

            @if($selectedNeed)
                <div class="rounded-2xl bg-sage-50 border border-sage-200 px-4 py-2.5 mb-3 text-[13px] text-sage-700">
                    Bu ihtiyaç için kalan tutar:
                    <span class="font-semibold">₺{{ number_format($selectedNeed->remainingAmount(), 0, ',', '.') }}</span>
                    — en fazla bu kadar bağışlayabilirsin.
                </div>
            @endif

            <div class="grid grid-cols-4 gap-2">
                @foreach([50, 100, 250, 500] as $preset)
                    <button type="button" wire:click="pickAmount({{ $preset }})"
                        @disabled($this->maxAmount() !== null && $preset > $this->maxAmount())
                        class="rounded-2xl py-3 text-center border-2 transition disabled:opacity-40 disabled:cursor-not-allowed
                        {{ (int) $amount === $preset && $customAmount === '' ? 'border-sage-600 bg-sage-50' : 'border-cream-300/60 bg-cream-100 hover:bg-cream-200' }}">
                        <div class="font-modern text-[20px] text-clay-600">₺{{ $preset }}</div>
                    </button>
                @endforeach
            </div>
            <div class="mt-3">
                <label class="text-[12px] text-ink-700/60">ya da kendi tutarın (₺)</label>
                <input type="text" wire:model.live="customAmount" placeholder="örn. 750"
                    class="mt-1 w-full rounded-2xl border-cream-300/60 bg-cream-50 text-ink-900 focus:ring-sage-400 focus:border-sage-400" />
            </div>

            <label class="flex items-center gap-2 mt-4 text-[13.5px] text-ink-700/80">
                <input type="checkbox" wire:model="isAnonymous" class="rounded border-cream-400 text-sage-600 focus:ring-sage-400" />
                Bağışım sıralamada anonim görünsün
            </label>

            @error('amount') <div class="text-[13px] text-clay-600 mt-2">{{ $message }}</div> @enderror

            <button wire:click="goToPayment"
                class="mt-5 w-full rounded-full bg-ink-900 hover:bg-ink-800 text-cream-50 py-3 text-[14px] font-medium">
                Ödemeye geç
            </button>
        </div>
    @endif

    @if($step === 2)
        {{-- ADIM 2 — GÖSTERMELİK ÖDEME --}}
        <div class="paper-card rounded-4xl p-6 sm:p-7 shadow-card border border-cream-300/50 mt-5">
            <div class="flex items-baseline justify-between">
                <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500">Ödeme bilgileri</div>
                <div class="font-modern text-[22px] text-ink-900">₺{{ number_format($amount, 0, ',', '.') }}</div>
            </div>
            <p class="text-[12px] text-ink-700/55 mt-1">
                Bu bir gösterim ekranıdır — gerçek bir ödeme alınmaz, kart bilgileriniz saklanmaz.
            </p>

            <div class="space-y-3 mt-4">
                <div>
                    <label class="text-[12px] text-ink-700/60">Kart Sahibi</label>
                    <input type="text" wire:model.blur="cardHolder" maxlength="60" autocomplete="cc-name"
                        class="mt-1 w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400" />
                    @error('cardHolder') <div class="text-[12px] text-clay-600 mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-[12px] text-ink-700/60">Kart Numarası</label>
                    <input type="text" wire:model.blur="cardNumber" placeholder="4242 4242 4242 4242"
                        inputmode="numeric" maxlength="23" autocomplete="cc-number"
                        class="mt-1 w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400" />
                    @error('cardNumber') <div class="text-[12px] text-clay-600 mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[12px] text-ink-700/60">Son Kullanma (AA/YY)</label>
                        <input type="text" wire:model.blur="cardExpiry" placeholder="05/28"
                            inputmode="numeric" maxlength="5" autocomplete="cc-exp"
                            x-on:input="
                                let d = $event.target.value.replace(/\D/g, '').slice(0, 4);
                                $event.target.value = d.length > 2 ? d.slice(0, 2) + '/' + d.slice(2) : d;
                            "
                            class="mt-1 w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400" />
                        @error('cardExpiry') <div class="text-[12px] text-clay-600 mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="text-[12px] text-ink-700/60">CVV</label>
                        <input type="text" wire:model.blur="cardCvv" placeholder="123"
                            inputmode="numeric" maxlength="4" autocomplete="cc-csc"
                            class="mt-1 w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400" />
                        @error('cardCvv') <div class="text-[12px] text-clay-600 mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            @error('amount') <div class="text-[13px] text-clay-600 mt-3">{{ $message }}</div> @enderror

            <div class="flex gap-3 mt-5">
                @unless($coverAllMode)
                    <button wire:click="$set('step', 1)" class="rounded-full bg-cream-100 hover:bg-cream-200 border border-cream-300/60 text-ink-700 px-5 py-3 text-[14px]">Geri</button>
                @endunless
                <button wire:click="donate" wire:loading.attr="disabled"
                    class="flex-1 rounded-full bg-ink-900 hover:bg-ink-800 text-cream-50 py-3 text-[14px] font-medium flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="donate">{{ $coverAllMode ? 'Tüm Masrafları Karşıla' : 'Bağışı Tamamla' }}</span>
                    <span wire:loading wire:target="donate">İşleniyor…</span>
                    <svg class="w-4 h-4" wire:loading.remove wire:target="donate"><use href="#paw" fill="currentColor"/></svg>
                </button>
            </div>
        </div>
    @endif

    @endif
</div>
