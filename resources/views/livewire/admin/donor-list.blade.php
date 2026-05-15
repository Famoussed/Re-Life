<div>
    <header class="mb-7">
        <h1 class="font-serif text-[34px] leading-tight text-ink-900">Bağışçılar</h1>
        <p class="text-[14px] text-ink-700/65 mt-1">Barınağına destek olan tüm dostlar.</p>
    </header>

    @if($donors->isEmpty())
        <div class="paper-card rounded-4xl border border-cream-300/50 p-12 text-center">
            <div class="font-hand text-[24px] text-clay-500">henüz bağışçınız yok</div>
        </div>
    @else
        <div class="paper-card rounded-3xl border border-cream-300/60 shadow-card overflow-hidden">
            <table class="w-full text-[14px]">
                <thead>
                    <tr class="text-left text-[12px] uppercase tracking-[0.1em] text-clay-500 border-b border-cream-300/60">
                        <th class="px-5 py-3 font-medium">#</th>
                        <th class="px-5 py-3 font-medium">Ad</th>
                        <th class="px-5 py-3 font-medium">E-posta</th>
                        <th class="px-5 py-3 font-medium text-right">Bağış Sayısı</th>
                        <th class="px-5 py-3 font-medium text-right">Toplam Bağış</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-300/40">
                    @foreach($donors as $i => $donor)
                        <tr wire:key="donor-{{ $donor['user']->id }}" class="hover:bg-cream-100/50">
                            <td class="px-5 py-3 font-serif text-clay-500">{{ $i + 1 }}</td>
                            <td class="px-5 py-3 text-ink-800">{{ $donor['user']->name }}</td>
                            <td class="px-5 py-3 text-ink-700/70">{{ $donor['user']->email }}</td>
                            <td class="px-5 py-3 text-right text-ink-700/70">{{ $donor['count'] }}</td>
                            <td class="px-5 py-3 text-right font-serif text-[15px] text-sage-700">
                                {{ number_format($donor['total'], 2, ',', '.') }} ₺
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
