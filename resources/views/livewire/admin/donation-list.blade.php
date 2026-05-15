<div>
    <header class="mb-7">
        <h1 class="font-serif text-[34px] leading-tight text-ink-900">Bağışlar</h1>
        <p class="text-[14px] text-ink-700/65 mt-1">Barınağına ulaşan tüm iyilikler.</p>
    </header>

    {{-- ÖZET --}}
    <div class="grid sm:grid-cols-2 gap-4 mb-6">
        <div class="paper-note rounded-3xl p-5 shadow-note">
            <div class="text-[11px] uppercase tracking-[0.14em] text-clay-500">Toplam Bağış</div>
            <div class="mt-2 font-serif text-[28px] text-ink-900">{{ number_format($total, 2, ',', '.') }} ₺</div>
        </div>
        <div class="paper-note rounded-3xl p-5 shadow-note">
            <div class="text-[11px] uppercase tracking-[0.14em] text-clay-500">Bağış Sayısı</div>
            <div class="mt-2 font-serif text-[28px] text-ink-900">{{ $count }}</div>
        </div>
    </div>

    {{-- FİLTRE --}}
    <div class="paper-card rounded-3xl border border-cream-300/60 p-4 mb-6 flex flex-wrap items-center gap-3">
        <span class="text-[13px] text-ink-700/70">Dönem:</span>
        @foreach(['' => 'Tümü', 'month' => 'Bu Ay', 'year' => 'Bu Yıl'] as $value => $label)
            <button wire:click="$set('period', '{{ $value }}')"
                class="rounded-full px-4 py-1.5 text-[13px] border {{ $period === $value ? 'bg-sage-600 text-cream-50 border-sage-600' : 'bg-cream-100 text-ink-700 border-cream-300/60 hover:bg-cream-200' }}">
                {{ $label }}
            </button>
        @endforeach
        @if($period)
            <button wire:click="resetFilters" class="text-[12px] text-clay-500 hover:text-clay-600 underline underline-offset-2">temizle</button>
        @endif
    </div>

    {{-- TABLO --}}
    @if($donations->isEmpty())
        <div class="paper-card rounded-4xl border border-cream-300/50 p-12 text-center">
            <div class="font-hand text-[24px] text-clay-500">bu dönemde bağış bulunamadı</div>
        </div>
    @else
        <div class="paper-card rounded-3xl border border-cream-300/60 shadow-card overflow-hidden">
            <table class="w-full text-[14px]">
                <thead>
                    <tr class="text-left text-[12px] uppercase tracking-[0.1em] text-clay-500 border-b border-cream-300/60">
                        <th class="px-5 py-3 font-medium">Bağışçı</th>
                        <th class="px-5 py-3 font-medium">Hedef</th>
                        <th class="px-5 py-3 font-medium text-right">Tutar</th>
                        <th class="px-5 py-3 font-medium text-right">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-300/40">
                    @foreach($donations as $donation)
                        <tr wire:key="donation-{{ $donation->id }}" class="hover:bg-cream-100/50">
                            <td class="px-5 py-3 text-ink-800">
                                {{ $donation->is_anonymous ? 'Anonim Bağışçı' : ($donation->user?->name ?? 'Anonim Bağışçı') }}
                            </td>
                            <td class="px-5 py-3 text-ink-700/75">
                                {{ $donation->animal?->name ?? 'Barınak geneli' }}
                            </td>
                            <td class="px-5 py-3 text-right font-serif text-[15px] text-sage-700">
                                {{ number_format((float) $donation->amount, 2, ',', '.') }} ₺
                            </td>
                            <td class="px-5 py-3 text-right text-ink-700/60">
                                {{ $donation->created_at->format('d.m.Y H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
