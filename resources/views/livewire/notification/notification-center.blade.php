<div class="max-w-[720px] mx-auto px-5 sm:px-8 py-8">
    <div class="flex items-baseline justify-between">
        <div>
            <div class="text-[12px] uppercase tracking-[0.16em] text-clay-500 font-medium">Senin için</div>
            <h1 class="font-modern text-[42px] text-ink-900 leading-tight mt-1">Bildirimler</h1>
        </div>
        @if($notifications->whereNull('read_at')->isNotEmpty())
            <button wire:click="markAllAsRead" class="text-[13px] text-sage-700 hover:text-sage-600 underline underline-offset-2">tümünü okundu işaretle</button>
        @endif
    </div>

    <div class="space-y-3 mt-5">
        @forelse($notifications as $n)
            @php($data = $n->data)
            <div wire:key="n-{{ $n->id }}"
                 class="paper-card rounded-3xl p-4 shadow-note border border-cream-300/50 flex items-start gap-3
                 {{ $n->read_at ? 'opacity-70' : '' }}">
                <div class="w-9 h-9 rounded-full bg-sun-100 border border-sun-200 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-clay-500"><use href="#heart" fill="currentColor"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-modern text-[17px] text-ink-900">{{ $data['title'] ?? 'Bildirim' }}</div>
                    <p class="text-[13.5px] text-ink-700/75 mt-0.5 leading-snug">{{ $data['message'] ?? '' }}</p>
                    <div class="flex items-center gap-3 mt-2">
                        @if(!empty($data['url']))
                            <a href="{{ $data['url'] }}" class="text-[12.5px] text-sage-700 hover:text-sage-600">görüntüle →</a>
                        @endif
                        @unless($n->read_at)
                            <button wire:click="markAsRead('{{ $n->id }}')" class="text-[12.5px] text-ink-700/55 hover:text-ink-900">okundu</button>
                        @endunless
                        <span class="text-[11px] text-ink-700/45 uppercase tracking-wider ml-auto">{{ $n->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="paper-card rounded-4xl border border-cream-300/50 p-12 text-center font-modern text-[24px] text-clay-500">
                henüz bildirimin yok 🌿
            </div>
        @endforelse
    </div>
</div>
