<div class="max-w-[1080px]">
    <div class="text-[12px] uppercase tracking-[0.16em] text-clay-500 font-medium">Platform Yönetimi</div>
    <h1 class="font-serif text-[40px] sm:text-[48px] leading-[0.95] text-ink-900 mt-1">Tüm Barınaklar</h1>
    <p class="text-[15px] text-ink-700/75 mt-2 max-w-[560px]">
        Platformdaki bütün barınakların durumunu görüntüleyin ve yönetin.
    </p>

    @if(session('status'))
        <div class="paper-note rounded-2xl shadow-note border border-sage-200 mt-5 px-5 py-3 text-[14px] text-sage-700">
            {{ session('status') }}
        </div>
    @endif

    {{-- Durum filtresi --}}
    <div class="flex flex-wrap gap-2 mt-6">
        <button wire:click="$set('statusFilter', '')"
                class="rounded-full px-5 py-2 text-[13.5px] border transition
                {{ $statusFilter === '' ? 'bg-sage-600 text-cream-50 border-sage-600 shadow-card' : 'bg-cream-100 text-ink-700 border-cream-300/60 hover:bg-cream-200' }}">
            Tümü
        </button>
        @foreach($statuses as $status)
            <button wire:click="$set('statusFilter', '{{ $status->value }}')"
                    class="rounded-full px-5 py-2 text-[13.5px] border transition
                    {{ $statusFilter === $status->value ? 'bg-sage-600 text-cream-50 border-sage-600 shadow-card' : 'bg-cream-100 text-ink-700 border-cream-300/60 hover:bg-cream-200' }}">
                {{ $status->label() }}
            </button>
        @endforeach
    </div>

    @php
        $statusClasses = [
            'pending' => 'bg-sun-100 text-clay-600',
            'approved' => 'bg-sage-100 text-sage-700',
            'rejected' => 'bg-peach-100 text-ink-900',
            'suspended' => 'bg-clay-100 text-clay-600',
        ];
    @endphp

    <div class="paper-card rounded-4xl shadow-card border border-cream-300/50 mt-5 overflow-hidden">
        @forelse($shelters as $i => $shelter)
            <div class="flex flex-wrap items-center gap-4 px-5 sm:px-6 py-4 {{ $i > 0 ? 'border-t border-dashed border-clay-200' : '' }}">
                <div class="flex-1 min-w-[180px]">
                    <a href="{{ route('shelters.show', $shelter) }}"
                       class="font-medium text-[15px] text-ink-900 hover:text-sage-700">
                        {{ $shelter->name }}
                    </a>
                    <div class="text-[12px] text-ink-700/60">{{ $shelter->city }}</div>
                </div>
                <div class="w-32">
                    <span class="inline-block rounded-full px-3 py-1 text-[12px] font-medium {{ $statusClasses[$shelter->status->value] ?? 'bg-cream-200 text-ink-700' }}">
                        {{ $shelter->status->label() }}
                    </span>
                </div>
                <div class="w-40 text-[13px] text-ink-700/75 hidden sm:block">
                    {{ $shelter->admin?->name ?? '—' }}
                </div>
                <div class="w-24 text-[13px] text-ink-700/75">
                    {{ $shelter->animals_count }} hayvan
                </div>
                <div class="w-32 text-right">
                    @if($shelter->status->value === 'approved')
                        <button wire:click="suspend({{ $shelter->id }})"
                                wire:confirm="“{{ $shelter->name }}” barınağını askıya almak istediğinize emin misiniz?"
                                class="rounded-full bg-peach-400 hover:bg-peach-300 text-ink-900 px-4 py-2 text-[13px] font-medium">
                            Askıya Al
                        </button>
                    @elseif($shelter->status->value === 'suspended')
                        <button wire:click="activate({{ $shelter->id }})"
                                wire:confirm="“{{ $shelter->name }}” barınağını aktive etmek istediğinize emin misiniz?"
                                class="rounded-full bg-sage-600 hover:bg-sage-700 text-cream-50 px-4 py-2 text-[13px] font-medium">
                            Aktive Et
                        </button>
                    @else
                        <span class="text-[12px] text-ink-700/40">—</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-12 text-center font-hand text-[24px] text-clay-500">
                bu kritere uygun barınak yok 🌱
            </div>
        @endforelse
    </div>
</div>
