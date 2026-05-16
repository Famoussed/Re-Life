@props(['animal'])

@php
    $tones = ['sage', 'clay', 'sun', 'peach', 'cream'];
    $tilts = ['tilt-1', 'tilt-2', 'tilt-3'];
    $tone = $tones[$animal->id % count($tones)];
    $tilt = $tilts[$animal->id % count($tilts)];
    $need = $animal->relationLoaded('activeNeeds')
        ? $animal->activeNeeds->first()
        : $animal->activeNeeds()->first();

    // Tür bazlı fallback görseller (photo_path yoksa)
    $fallbackImages = [
        'dog'    => [
            'https://images.unsplash.com/photo-1552053831-71594a27632d?q=80&w=700&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1587300003388-59208cc962cb?q=80&w=700&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1537151625747-768eb6cf92b2?q=80&w=700&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1477884213360-7e9d7dcc1e48?q=80&w=700&auto=format&fit=crop',
        ],
        'puppy'  => [
            'https://images.unsplash.com/photo-1548199973-03cce0bbc87b?q=80&w=700&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?q=80&w=700&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1600804340584-c7db2eacf0bf?q=80&w=700&auto=format&fit=crop',
        ],
        'cat'    => [
            'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?q=80&w=700&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1533743983669-94fa5c4338ec?q=80&w=700&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1573865526739-10659fec78a5?q=80&w=700&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1495360010541-f48722b34f7d?q=80&w=700&auto=format&fit=crop',
        ],
        'kitten' => [
            'https://images.unsplash.com/photo-1574158622682-e40e69881006?q=80&w=700&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1529778873920-4da4926a72c2?q=80&w=700&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1596854407944-bf87f6fdd049?q=80&w=700&auto=format&fit=crop',
        ],
    ];

    $speciesKey = $animal->species->value ?? 'dog';
    $pool = $fallbackImages[$speciesKey] ?? $fallbackImages['dog'];
    $fallbackUrl = $pool[$animal->id % count($pool)];

    $photoStyle = $animal->photo_path
        ? "background-image:url('" . \Illuminate\Support\Facades\Storage::url($animal->photo_path) . "')"
        : "background-image:url('{$fallbackUrl}')";
@endphp

<a href="{{ route('animals.show', $animal) }}"
   class="soul-card paper-card rounded-4xl shadow-card border border-cream-300/50 overflow-hidden {{ $tilt }} block">
    <div class="relative">
        {{-- Fotoğraf --}}
        <div class="photo photo-vignette {{ $tone }} h-[260px] bg-cover bg-center" style="{{ $photoStyle }}"></div>
        {{-- Tür Etiketi --}}
        <div class="absolute top-3 left-3 rounded-full bg-white/85 backdrop-blur-sm text-ink-700 px-3 py-1 text-[11px] uppercase tracking-widest font-semibold border border-cream-300/60">
            {{ $animal->species->label() }}
        </div>
        {{-- Şehir Etiketi --}}
        <div class="absolute top-3 right-3 rounded-full bg-white/85 backdrop-blur-sm text-sage-700 px-3 py-1 text-[11px] font-semibold border border-sage-200/60">
            {{ $animal->shelter->city }}
        </div>
    </div>

    <div class="p-5">
        {{-- İsim + Yaş --}}
        <div class="flex items-baseline justify-between gap-3">
            <h3 class="font-modern text-[30px] leading-[1] text-ink-900 tracking-tight">{{ $animal->name }}</h3>
            <span class="text-[11px] text-ink-700/50 font-medium tracking-wide uppercase">{{ $animal->age_estimate }}</span>
        </div>

        {{-- Hikâye Özeti --}}
        <p class="mt-2 text-[13.5px] text-ink-700/70 leading-relaxed line-clamp-2">
            {{ \Illuminate\Support\Str::limit($animal->story, 80) }}
        </p>

        @if($need)
            <div class="mt-5">
                <div class="flex items-baseline justify-between mb-2">
                    <span class="text-[13.5px] font-semibold text-ink-900 tracking-tight">{{ $need->title }}</span>
                    <span class="text-[11px] text-clay-500 font-bold uppercase tracking-wider">%{{ $need->progressPercent() }}</span>
                </div>
                <x-progress :percent="$need->progressPercent()" />
                <div class="flex items-baseline justify-between mt-2 text-[12px] text-ink-700/60">
                    <span>
                        <span class="text-ink-900 font-bold">₺{{ number_format((float) $need->collected_amount, 0, ',', '.') }}</span>
                        <span class="text-ink-700/40"> / ₺{{ number_format((float) $need->target_amount, 0, ',', '.') }}</span>
                    </span>
                    <span class="uppercase tracking-wide text-[11px]">{{ $need->type->label() }}</span>
                </div>
            </div>
        @else
            <div class="mt-5 text-[12.5px] text-ink-700/50 leading-relaxed">Şu an aktif ihtiyacı yok — yine de barınağına destek olabilirsin.</div>
        @endif

        <div class="mt-5 pt-4 border-t border-cream-300/70 flex items-center justify-between">
            <span class="text-[13px] text-sage-700 font-semibold tracking-wide">Hikâyesini oku</span>
            <svg class="w-4 h-4 text-sage-600"><use href="#arrow" stroke="currentColor"/></svg>
        </div>
    </div>
</a>

