@props(['animal'])

@php
    $tones = ['sage', 'clay', 'sun', 'peach', 'cream'];
    $tilts = ['tilt-1', 'tilt-2', 'tilt-3'];
    $tone = $tones[$animal->id % count($tones)];
    $tilt = $tilts[$animal->id % count($tilts)];
    $need = $animal->relationLoaded('activeNeeds')
        ? $animal->activeNeeds->first()
        : $animal->activeNeeds()->first();
@endphp

<a href="{{ route('animals.show', $animal) }}"
   class="soul-card paper-card rounded-4xl shadow-card border border-cream-300/50 overflow-hidden {{ $tilt }} block">
    <div class="relative">
        <x-photo :tone="$tone" :path="$animal->photo_path" :label="mb_strtolower($animal->name).' · doğal portre'" class="h-[260px]" />
        <div class="absolute top-3 left-3 rounded-full bg-cream-100/90 text-clay-500 px-3 py-1 text-[11px] uppercase tracking-wider font-medium border border-cream-300/60">
            {{ $animal->species->label() }}
        </div>
        <div class="absolute top-3 right-3 rounded-full bg-sage-100/90 text-sage-700 px-3 py-1 text-[11px] border border-sage-200">
            {{ $animal->shelter->city }}
        </div>
    </div>

    <div class="p-5">
        <div class="flex items-baseline justify-between gap-3">
            <h3 class="font-serif text-[34px] leading-[0.95] text-ink-900">{{ $animal->name }}</h3>
            <span class="text-[12px] text-ink-700/60">{{ $animal->age_estimate }}</span>
        </div>
        <div class="font-hand text-[19px] text-clay-500 leading-tight mt-1">{{ \Illuminate\Support\Str::limit($animal->story, 64) }}</div>

        @if($need)
            <div class="mt-5">
                <div class="flex items-baseline justify-between mb-2">
                    <span class="font-serif italic text-[16px] text-ink-900">{{ $need->title }}</span>
                    <span class="text-[11px] text-clay-500 font-medium uppercase tracking-wider">%{{ $need->progressPercent() }}</span>
                </div>
                <x-progress :percent="$need->progressPercent()" />
                <div class="flex items-baseline justify-between mt-2 text-[12px] text-ink-700/65">
                    <span><span class="text-ink-900 font-semibold">₺{{ number_format((float) $need->collected_amount, 0, ',', '.') }}</span>
                        <span class="text-ink-700/45">/ ₺{{ number_format((float) $need->target_amount, 0, ',', '.') }}</span></span>
                    <span>{{ $need->type->label() }}</span>
                </div>
            </div>
        @else
            <div class="mt-5 text-[13px] text-ink-700/55 italic">Şu an aktif ihtiyacı yok — yine de barınağına destek olabilirsin.</div>
        @endif

        <div class="mt-5 pt-4 border-t border-dashed border-clay-200 flex items-center justify-between text-[13.5px] text-sage-700 font-medium">
            <span>Hikâyesini oku</span>
            <svg class="w-4 h-4"><use href="#arrow" stroke="currentColor"/></svg>
        </div>
    </div>
</a>
