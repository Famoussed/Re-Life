<div>
    <header class="mb-7">
        <div class="font-modern text-[22px] text-clay-500">merhaba</div>
        <h1 class="font-modern text-[34px] sm:text-[40px] leading-tight text-ink-900">
            Hoş geldin, {{ $shelter->name }}
        </h1>
        <p class="text-[14px] text-ink-700/65 mt-1">Barınağının bugünkü durumuna kuşbakışı.</p>
    </header>

    {{-- İSTATİSTİK KARTLARI --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <div class="paper-note rounded-3xl p-5 shadow-note">
            <div class="text-[11px] uppercase tracking-[0.14em] text-clay-500">Bu Ay</div>
            <div class="mt-2 font-modern text-[26px] text-ink-900">{{ number_format($monthTotal, 2, ',', '.') }} ₺</div>
            <div class="text-[12px] text-ink-700/60 mt-1">bağış toplandı</div>
        </div>
        <div class="paper-note rounded-3xl p-5 shadow-note">
            <div class="text-[11px] uppercase tracking-[0.14em] text-clay-500">Bu Yıl</div>
            <div class="mt-2 font-modern text-[26px] text-ink-900">{{ number_format($yearTotal, 2, ',', '.') }} ₺</div>
            <div class="text-[12px] text-ink-700/60 mt-1">bağış toplandı</div>
        </div>
        <div class="paper-note rounded-3xl p-5 shadow-note">
            <div class="text-[11px] uppercase tracking-[0.14em] text-clay-500">Tüm Zamanlar</div>
            <div class="mt-2 font-modern text-[26px] text-ink-900">{{ number_format($allTimeTotal, 2, ',', '.') }} ₺</div>
            <div class="text-[12px] text-ink-700/60 mt-1">toplam bağış</div>
        </div>
        <div class="paper-note rounded-3xl p-5 shadow-note">
            <div class="text-[11px] uppercase tracking-[0.14em] text-clay-500">Aktif Dost</div>
            <div class="mt-2 font-modern text-[26px] text-ink-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-sage-600"><use href="#paw" fill="currentColor"/></svg>
                {{ $activeAnimals }}
            </div>
            <div class="text-[12px] text-ink-700/60 mt-1">barınakta aktif</div>
        </div>
        <div class="paper-note rounded-3xl p-5 shadow-note">
            <div class="text-[11px] uppercase tracking-[0.14em] text-clay-500">Aktif İhtiyaç</div>
            <div class="mt-2 font-modern text-[26px] text-ink-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-clay-500"><use href="#heart" fill="currentColor"/></svg>
                {{ $activeNeeds }}
            </div>
            <div class="text-[12px] text-ink-700/60 mt-1">açık ihtiyaç</div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- SON BAĞIŞLAR --}}
        <div class="paper-card rounded-3xl border border-cream-300/60 p-6 shadow-card">
            <h2 class="font-modern text-[22px] text-ink-900 mb-4">Son Bağışlar</h2>
            @if($recentDonations->isEmpty())
                <p class="text-[14px] text-ink-700/60">Henüz bağış yok.</p>
            @else
                <ul class="divide-y divide-cream-300/50">
                    @foreach($recentDonations as $donation)
                        <li class="flex items-center justify-between py-2.5" wire:key="recent-{{ $donation->id }}">
                            <div>
                                <div class="text-[14px] text-ink-800">
                                    {{ $donation->is_anonymous ? 'Anonim' : ($donation->user?->name ?? 'Anonim') }}
                                </div>
                                <div class="text-[12px] text-ink-700/55">{{ $donation->created_at->format('d.m.Y H:i') }}</div>
                            </div>
                            <div class="font-modern text-[17px] text-sage-700">{{ number_format((float) $donation->amount, 2, ',', '.') }} ₺</div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- EN ÇOK BAĞIŞ YAPANLAR --}}
        <div class="paper-card rounded-3xl border border-cream-300/60 p-6 shadow-card">
            <h2 class="font-modern text-[22px] text-ink-900 mb-4">En Çok Bağış Yapan Dostlar</h2>
            @if($topDonors->isEmpty())
                <p class="text-[14px] text-ink-700/60">Henüz bağış yok.</p>
            @else
                <ol class="space-y-1.5">
                    @foreach($topDonors as $i => $donor)
                        <li class="flex items-center justify-between py-2 px-3 rounded-2xl {{ $i < 3 ? 'bg-sun-50' : '' }}" wire:key="top-{{ $i }}">
                            <div class="flex items-center gap-3">
                                <span class="font-modern text-[15px] text-clay-500 w-6">{{ $i + 1 }}.</span>
                                <span class="text-[14px] text-ink-800">{{ $donor['user']?->name ?? 'Anonim' }}</span>
                            </div>
                            <div class="text-right">
                                <div class="font-modern text-[16px] text-sage-700">{{ number_format($donor['total'], 2, ',', '.') }} ₺</div>
                                <div class="text-[11px] text-ink-700/55">{{ $donor['count'] }} bağış</div>
                            </div>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    </div>
</div>
