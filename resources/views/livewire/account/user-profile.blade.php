<div class="max-w-[1000px] mx-auto px-5 sm:px-8 py-8">
    {{-- Profil başlığı --}}
    <div class="paper-card rounded-4xl p-6 sm:p-8 shadow-card border border-cream-300/50 relative overflow-hidden">
        <svg class="absolute -right-8 -top-8 w-44 h-44 text-clay-200/35"><use href="#heart" fill="currentColor"/></svg>
        <div class="relative flex items-center gap-5 flex-wrap">
            <div class="w-20 h-20 rounded-full bg-sage-200 flex items-center justify-center font-modern text-[36px] text-sage-700">
                {{ mb_substr($user->name, 0, 1) }}
            </div>
            <div>
                <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500 font-medium">Dostlardan biri</div>
                <h1 class="font-modern text-[42px] leading-none text-ink-900 mt-1">{{ $user->name }}</h1>
                @if($badge)
                    <div class="font-modern text-[18px] text-clay-500 mt-1">🏅 {{ $badge->name }}</div>
                @else
                    <div class="font-modern text-[22px] text-clay-500 mt-1">yolculuğun henüz başında</div>
                @endif
            </div>
            <div class="flex-1"></div>
            @if($isOwner)
                <a href="{{ route('profile') }}" class="rounded-full bg-cream-100 hover:bg-cream-200 border border-cream-300/60 text-ink-700 px-5 py-2.5 text-[13px]">Profili düzenle</a>
            @endif
        </div>

        <div class="flex flex-wrap gap-3 mt-6">
            <div class="paper-note rounded-2xl px-5 py-3">
                <div class="font-modern text-[26px] text-clay-600">₺{{ number_format((float) $user->total_donated, 0, ',', '.') }}</div>
                <div class="text-[11px] text-ink-700/65 uppercase tracking-wider">toplam bağış</div>
            </div>
            <div class="paper-note rounded-2xl px-5 py-3">
                <div class="font-modern text-[26px] text-clay-600">{{ $donationCount }}</div>
                <div class="text-[11px] text-ink-700/65 uppercase tracking-wider">bağış adımı</div>
            </div>
            <div class="paper-note rounded-2xl px-5 py-3">
                <div class="font-modern text-[26px] text-clay-600">Sv. {{ $user->badge_level }}</div>
                <div class="text-[11px] text-ink-700/65 uppercase tracking-wider">rozet seviyesi</div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-12 gap-7 mt-7">
        {{-- Bağış geçmişi --}}
        <div class="lg:col-span-7">
            <h2 class="font-modern text-[26px] text-ink-900 mb-3">Bağış geçmişi</h2>
            <div class="paper-card rounded-4xl shadow-card border border-cream-300/50 overflow-hidden">
                @forelse($donations as $i => $d)
                    <div class="flex items-center gap-4 px-5 py-3.5 {{ $i > 0 ? 'border-t border-dashed border-clay-200' : '' }}">
                        <svg class="w-6 h-6 text-clay-400"><use href="#paw" fill="currentColor"/></svg>
                        <div class="flex-1 min-w-0">
                            @if($d->is_anonymous && ! $isOwner)
                                <div class="text-[14px] text-ink-700/70 italic">Anonim bağış</div>
                            @else
                                <div class="text-[14px] text-ink-900">
                                    {{ $d->animal?->name ?? $d->shelter->name }}
                                    @if($d->is_anonymous)<span class="text-[11px] text-clay-500">· anonim</span>@endif
                                </div>
                                <div class="text-[12px] text-ink-700/55">{{ $d->shelter->name }}</div>
                            @endif
                        </div>
                        <div class="text-right">
                            <div class="font-modern text-[17px] text-ink-900">₺{{ number_format((float) $d->amount, 0, ',', '.') }}</div>
                            <div class="text-[11px] text-ink-700/50">{{ $d->created_at->format('d.m.Y') }}</div>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center font-modern text-[22px] text-clay-500">henüz bir adım atılmadı</div>
                @endforelse
            </div>
        </div>

        {{-- Desteklenen hayvanlar --}}
        <div class="lg:col-span-5">
            <h2 class="font-modern text-[26px] text-ink-900 mb-3">Desteklediği dostlar</h2>
            <div class="paper-card rounded-4xl shadow-card border border-cream-300/50 p-5">
                @if($supportedAnimals->isEmpty())
                    <div class="text-center font-modern text-[20px] text-clay-500 py-6">henüz yok</div>
                @else
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($supportedAnimals as $animal)
                            <a href="{{ route('animals.show', $animal) }}" class="block">
                                <x-photo :tone="['sage','clay','sun','peach','cream'][$animal->id % 5]"
                                         :path="$animal->photo_path" :label="mb_strtolower($animal->name)"
                                         class="h-24 rounded-2xl" />
                                <div class="text-[13px] text-ink-800 mt-1 font-medium">{{ $animal->name }}</div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Teşekkür belgeleri --}}
    <div class="mt-7">
        <h2 class="font-modern text-[26px] text-ink-900 mb-3">Teşekkür Belgeleri</h2>
        @if($certificates->isEmpty())
            <div class="paper-card rounded-4xl shadow-card border border-cream-300/50 p-10 text-center font-modern text-[22px] text-clay-500">
                henüz belge yok
            </div>
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($certificates as $certificate)
                    <a href="{{ route('certificates.show', $certificate) }}"
                       class="paper-card rounded-3xl border border-cream-300/60 shadow-note p-5 block hover:-translate-y-0.5 transition-transform"
                       wire:key="certificate-{{ $certificate->id }}">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-7 h-7 text-sun-400"><use href="#paw" fill="currentColor"/></svg>
                            <div class="text-[11px] uppercase tracking-[0.14em] text-clay-500">Teşekkür Belgesi</div>
                        </div>
                        <div class="font-modern text-[19px] text-ink-900 mt-2">
                            {{ $certificate->animal_name ?? 'Barınağa destek' }}
                        </div>
                        <div class="flex items-baseline justify-between mt-3">
                            <span class="font-modern text-[20px] text-clay-600">₺{{ number_format((float) $certificate->amount, 0, ',', '.') }}</span>
                            <span class="text-[12px] text-ink-700/55">{{ $certificate->issued_at->format('d.m.Y') }}</span>
                        </div>
                        <div class="mt-3 pt-3 border-t border-cream-300/70 flex items-center justify-between">
                            <span class="text-[11px] text-ink-700/45 tracking-wide">{{ $certificate->certificate_no }}</span>
                            <span class="text-[12.5px] text-sage-700 font-medium">Görüntüle →</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
