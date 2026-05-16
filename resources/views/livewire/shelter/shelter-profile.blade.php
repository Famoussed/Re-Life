<div class="max-w-[1320px] mx-auto px-5 sm:px-8 py-8">
    <a href="{{ route('home') }}" class="text-[13px] text-sage-700 hover:text-sage-600">← Anasayfaya dön</a>

    {{-- Barınak başlığı --}}
    <div class="paper-card rounded-4xl p-6 sm:p-8 shadow-card border border-cream-300/50 mt-4 relative overflow-hidden">
        <svg class="absolute -right-6 -top-6 w-40 h-40 text-sage-200/40"><use href="#paw" fill="currentColor"/></svg>
        <div class="relative">
            <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500 font-medium">Barınak · {{ $shelter->city }}</div>
            <h1 class="font-serif text-[44px] sm:text-[56px] leading-[0.95] text-ink-900 mt-1">{{ $shelter->name }}</h1>
            <div class="flex flex-wrap gap-5 mt-4 text-[14px] text-ink-700/75">
                <span>📍 {{ $shelter->address }}</span>
                <span>📞 {{ $shelter->phone }}</span>
            </div>
            <div class="flex flex-wrap gap-3 mt-5">
                <div class="paper-note rounded-2xl px-4 py-2.5">
                    <div class="font-serif text-[22px] text-clay-600">{{ $animals->count() }}</div>
                    <div class="text-[11px] text-ink-700/65 uppercase tracking-wider">yayında dost</div>
                </div>
                <div class="paper-note rounded-2xl px-4 py-2.5">
                    <div class="font-serif text-[22px] text-clay-600">₺{{ number_format($totalRaised, 0, ',', '.') }}</div>
                    <div class="text-[11px] text-ink-700/65 uppercase tracking-wider">toplam destek</div>
                </div>
            </div>
            <a href="{{ route('donate', ['shelter' => $shelter->id]) }}"
               class="mt-5 inline-flex items-center gap-2 rounded-full bg-ink-900 hover:bg-ink-800 text-cream-50 px-6 py-3 text-[14px] font-medium">
                Barınağa Genel Destek Ol
                <svg class="w-4 h-4"><use href="#heart" fill="currentColor"/></svg>
            </a>
        </div>
    </div>

    {{-- Duyurular --}}
    @if($announcements->isNotEmpty())
        <div class="mt-7">
            <h2 class="font-serif text-[26px] text-ink-900 mb-3">Barınaktan haberler</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                @foreach($announcements as $a)
                    <div class="paper-note rounded-2xl p-4 shadow-note note-tilt-1">
                        <div class="font-serif text-[17px] text-ink-900">{{ $a->title }}</div>
                        <p class="text-[13.5px] text-ink-700/70 mt-1 leading-snug">{{ $a->body }}</p>
                        <div class="text-[11px] text-ink-700/50 mt-2 uppercase tracking-wider">{{ $a->created_at->diffForHumans() }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Hayvanlar --}}
    <div class="mt-8">
        <h2 class="font-serif text-[30px] text-ink-900 mb-4">Buradaki dostlar</h2>
        @if($animals->isEmpty())
            <div class="paper-card rounded-4xl border border-cream-300/50 p-10 text-center font-hand text-[22px] text-clay-500">
                bu barınakta henüz yayında dost yok
            </div>
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-7">
                @foreach($animals as $animal)
                    <x-animal-card :animal="$animal" wire:key="sa-{{ $animal->id }}" />
                @endforeach
            </div>
        @endif
    </div>
</div>
