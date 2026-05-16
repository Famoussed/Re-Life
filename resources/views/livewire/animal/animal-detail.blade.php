<div class="max-w-[1320px] mx-auto px-5 sm:px-8 py-8">
    @php
        $tones = ['sage', 'clay', 'sun', 'peach', 'cream'];
        $tone = $tones[$animal->id % count($tones)];
        $activeNeeds = $animal->needs->where('status', \App\Enums\Animal\NeedStatus::Active);
        $completedNeeds = $animal->needs->where('status', \App\Enums\Animal\NeedStatus::Completed);
    @endphp

    <a href="{{ route('home') }}" class="text-[13px] text-sage-700 hover:text-sage-600">← Tüm dostlara dön</a>

    <div class="grid lg:grid-cols-12 gap-8 mt-4">
        {{-- SOL — foto + hikâye --}}
        <div class="lg:col-span-7">
            <div class="paper-card rounded-4xl p-6 sm:p-7 shadow-card border border-cream-300/50">
                <x-photo :tone="$tone" :path="$animal->photo_path"
                         :label="mb_strtolower($animal->name).' · sıcak portre'"
                         class="w-full h-[360px] rounded-3xl" />

                <div class="mt-5 flex items-baseline justify-between flex-wrap gap-2">
                    <div>
                        <div class="text-[11px] uppercase tracking-[0.16em] text-sage-700 font-medium">
                            {{ $animal->species->label() }} · {{ $animal->gender->label() }}
                        </div>
                        <h1 class="font-serif text-[52px] leading-[0.9] text-ink-900 mt-1">{{ $animal->name }}</h1>
                        <div class="font-hand text-[23px] text-clay-500 mt-1">{{ $animal->age_estimate }}</div>
                    </div>
                    <a href="{{ route('shelters.show', $animal->shelter) }}"
                       class="rounded-full bg-sage-100 text-sage-700 px-4 py-2 text-[13px] border border-sage-200 hover:bg-sage-200">
                        {{ $animal->shelter->name }} · {{ $animal->shelter->city }}
                    </a>
                </div>

                <div class="mt-6 pt-5 border-t border-dashed border-clay-200">
                    <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500 mb-1.5">Hikâyesi</div>
                    <p class="font-serif text-[18px] text-ink-900/90 leading-[1.6]">{{ $animal->story }}</p>
                </div>

                <div class="mt-5">
                    <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500 mb-1.5">Sağlık durumu</div>
                    <p class="text-[14.5px] text-ink-700/80 leading-[1.6]">{{ $animal->health_status }}</p>
                </div>
            </div>
        </div>

        {{-- SAĞ — ihtiyaçlar --}}
        <div class="lg:col-span-5 space-y-5">
            <div class="paper-card rounded-4xl p-6 sm:p-7 shadow-card border border-cream-300/50">
                <h2 class="font-serif text-[28px] text-ink-900 leading-tight">İyileşme yolculuğu</h2>
                <p class="text-[13px] text-ink-700/60 mt-1">Her ihtiyaç, ona atılan bir adım.</p>

                <div class="space-y-4 mt-5">
                    @forelse($activeNeeds as $need)
                        <div class="paper-note rounded-2xl p-4 shadow-note">
                            <div class="flex items-baseline justify-between gap-3">
                                <div class="font-serif text-[17px] text-ink-900">{{ $need->title }}</div>
                                <span class="rounded-full bg-cream-100 text-clay-500 px-2.5 py-0.5 text-[11px] border border-cream-300/60 whitespace-nowrap">{{ $need->type->label() }}</span>
                            </div>
                            @if($need->description)
                                <p class="text-[13px] text-ink-700/65 mt-1 leading-snug">{{ $need->description }}</p>
                            @endif
                            <div class="mt-3"><x-progress :percent="$need->progressPercent()" /></div>
                            <div class="flex items-baseline justify-between mt-2 text-[12.5px] text-ink-700/70">
                                <span><span class="text-ink-900 font-semibold">₺{{ number_format((float) $need->collected_amount, 0, ',', '.') }}</span>
                                    <span class="text-ink-700/45">/ ₺{{ number_format((float) $need->target_amount, 0, ',', '.') }}</span></span>
                                <span class="text-clay-500 font-medium">%{{ $need->progressPercent() }}</span>
                            </div>
                            <a href="{{ route('donate', ['need' => $need->id]) }}"
                               class="mt-3 w-full rounded-full bg-ink-900 hover:bg-ink-800 text-cream-50 py-2.5 text-[13.5px] font-medium flex items-center justify-center gap-2">
                                Ona Bir Adım Hediye Et
                                <svg class="w-4 h-4"><use href="#paw" fill="currentColor"/></svg>
                            </a>
                        </div>
                    @empty
                        <div class="rounded-2xl bg-sage-50 border border-sage-200 p-4 text-[14px] text-sage-700">
                            Şu an aktif bir ihtiyacı yok — yine de barınağına genel destek olabilirsin.
                        </div>
                    @endforelse

                    @foreach($completedNeeds as $need)
                        <div class="rounded-2xl bg-sage-50 border border-sage-200 p-4">
                            <div class="flex items-center justify-between">
                                <span class="font-serif text-[15px] text-ink-900">{{ $need->title }}</span>
                                <span class="text-[12px] text-sage-700 font-medium">Tamamlandı ✓</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <a href="{{ route('donate', ['shelter' => $animal->shelter_id]) }}"
                   class="mt-5 block text-center rounded-full bg-cream-100 hover:bg-cream-200 border border-cream-300/60 text-ink-700 text-[13px] py-2.5">
                    {{ $animal->shelter->name }} barınağına genel destek
                </a>
            </div>
        </div>
    </div>
</div>
