<div class="max-w-[820px]">
    <div class="text-[12px] uppercase tracking-[0.16em] text-clay-500 font-medium">Platform Yönetimi</div>
    <h1 class="font-serif text-[40px] sm:text-[48px] leading-[0.95] text-ink-900 mt-1">Rozetler</h1>
    <p class="text-[15px] text-ink-700/75 mt-2 max-w-[560px]">
        Rozet seviyeleri, bağışçıların toplam bağışına göre kazandığı ödüllerdir.
    </p>

    @if(session('status'))
        <div class="paper-note rounded-2xl shadow-note border border-sage-200 mt-5 px-5 py-3 text-[14px] text-sage-700">
            {{ session('status') }}
        </div>
    @endif

    <div class="paper-note rounded-2xl shadow-note border border-sun-200 mt-5 px-5 py-4 text-[13.5px] text-ink-800">
        <span class="font-hand text-[18px] text-clay-500">Not:</span>
        Her rozetin <strong>minimum tutarı</strong>, bir bağışçının o seviyeye ulaşmak için
        toplamda bağışlaması gereken eşiği belirler. Seviyeler küçükten büyüğe doğru sıralıdır.
    </div>

    <form wire:submit="save" class="mt-6">
        <div class="paper-card rounded-4xl shadow-card border border-cream-300/50 overflow-hidden">
            <div class="hidden sm:flex items-center gap-4 px-6 py-3 bg-cream-100 text-[12px] uppercase tracking-[0.12em] text-clay-500 font-medium">
                <div class="w-16">Seviye</div>
                <div class="flex-1">Rozet Adı</div>
                <div class="w-48">Min. Bağış Tutarı (₺)</div>
            </div>

            @forelse($badgeModels as $i => $badge)
                <div class="flex flex-wrap items-center gap-4 px-5 sm:px-6 py-4 {{ $i > 0 ? 'border-t border-dashed border-clay-200' : '' }}">
                    <div class="w-16 font-serif text-[24px] text-clay-500">{{ $badge->level }}</div>
                    <div class="flex-1 min-w-[180px]">
                        <input type="text" wire:model="badges.{{ $badge->id }}.name"
                               class="w-full rounded-2xl border border-cream-300/70 bg-cream-50 px-4 py-2.5 text-[14px] text-ink-900 focus:outline-none focus:border-sage-400">
                        @error('badges.'.$badge->id.'.name')
                            <div class="text-[12px] text-peach-400 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="w-48">
                        <input type="number" step="0.01" min="0" wire:model="badges.{{ $badge->id }}.min_amount"
                               class="w-full rounded-2xl border border-cream-300/70 bg-cream-50 px-4 py-2.5 text-[14px] text-ink-900 focus:outline-none focus:border-sage-400">
                        @error('badges.'.$badge->id.'.min_amount')
                            <div class="text-[12px] text-peach-400 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            @empty
                <div class="p-12 text-center font-hand text-[24px] text-clay-500">
                    henüz rozet tanımı yok 🌱
                </div>
            @endforelse
        </div>

        @if($badgeModels->isNotEmpty())
            <div class="mt-5 flex justify-end">
                <button type="submit"
                        class="rounded-full bg-sage-600 hover:bg-sage-700 text-cream-50 px-5 py-2.5 text-[14px] font-medium">
                    Tümünü Kaydet
                </button>
            </div>
        @endif
    </form>
</div>
