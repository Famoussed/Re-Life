<div class="max-w-[900px] mx-auto px-5 sm:px-8 py-8">
    <div class="text-[12px] uppercase tracking-[0.16em] text-clay-500 font-medium">Birlikte daha güçlü</div>
    <h1 class="font-modern text-[44px] sm:text-[56px] leading-[0.95] text-ink-900 mt-1">Dostluk Sıralaması</h1>
    <p class="text-[15px] text-ink-700/75 mt-2 max-w-[520px]">
        En çok adım hediye eden 100 dost. Anonim bağışçılar isimsiz görünür ama yürekleri burada.
    </p>

    {{-- Sekmeler --}}
    <div class="flex gap-2 mt-6">
        @foreach(['all' => 'Tüm Zamanlar', 'year' => 'Bu Yıl', 'month' => 'Bu Ay'] as $key => $label)
            <button wire:click="setTab('{{ $key }}')"
                class="rounded-full px-5 py-2 text-[13.5px] border transition
                {{ $tab === $key ? 'bg-sage-600 text-cream-50 border-sage-600 shadow-card' : 'bg-cream-100 text-ink-700 border-cream-300/60 hover:bg-cream-200' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <div class="paper-card rounded-4xl shadow-card border border-cream-300/50 mt-5 overflow-hidden">
        @forelse($rows as $i => $row)
            @php($rank = $i + 1)
            <div class="flex items-center gap-4 px-5 sm:px-6 py-3.5 {{ $i > 0 ? 'border-t border-dashed border-clay-200' : '' }}">
                <div class="w-9 text-center font-modern text-[22px] {{ $rank <= 3 ? 'text-clay-500' : 'text-ink-700/40' }}">
                    {{ $rank }}
                </div>
                <div class="w-10 h-10 rounded-full bg-sage-200 flex items-center justify-center font-modern text-[16px] text-sage-700 shrink-0">
                    {{ $row['anonymous'] ? '🐾' : mb_substr($row['user']->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    @if($row['anonymous'])
                        <div class="font-medium text-[15px] text-ink-700/70">Anonim Bağışçı</div>
                    @else
                        <a href="{{ route('users.show', $row['user']) }}" class="font-medium text-[15px] text-ink-900 hover:text-sage-700">
                            {{ $row['user']->name }}
                        </a>
                    @endif
                    @if($row['badge_level'] > 0)
                        <div class="text-[11.5px] text-clay-500">🏅 Seviye {{ $row['badge_level'] }} rozet</div>
                    @endif
                </div>
                <div class="font-modern text-[20px] text-ink-900">₺{{ number_format($row['amount'], 0, ',', '.') }}</div>
            </div>
        @empty
            <div class="p-12 text-center font-modern text-[24px] text-clay-500">
                bu dönemde henüz bağış yok — ilk adımı sen at 🌱
            </div>
        @endforelse
    </div>
</div>
