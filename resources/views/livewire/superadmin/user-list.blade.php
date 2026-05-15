<div class="max-w-[1080px]">
    <div class="text-[12px] uppercase tracking-[0.16em] text-clay-500 font-medium">Platform Yönetimi</div>
    <h1 class="font-serif text-[40px] sm:text-[48px] leading-[0.95] text-ink-900 mt-1">Kullanıcılar</h1>
    <p class="text-[15px] text-ink-700/75 mt-2 max-w-[560px]">
        Platformdaki tüm kullanıcıları görüntüleyin ve gerektiğinde erişimlerini yönetin.
    </p>

    @if(session('status'))
        <div class="paper-note rounded-2xl shadow-note border border-sage-200 mt-5 px-5 py-3 text-[14px] text-sage-700">
            {{ session('status') }}
        </div>
    @endif

    {{-- Arama --}}
    <div class="mt-6">
        <input type="text" wire:model.live.debounce.300ms="search"
               placeholder="İsim veya e-posta ile ara…"
               class="w-full sm:w-80 rounded-full border border-cream-300/70 bg-cream-50 px-5 py-2.5 text-[14px] text-ink-900 placeholder:text-ink-700/40 focus:outline-none focus:border-sage-400">
    </div>

    <div class="paper-card rounded-4xl shadow-card border border-cream-300/50 mt-5 overflow-hidden">
        @forelse($users as $i => $user)
            <div class="flex flex-wrap items-center gap-4 px-5 sm:px-6 py-4 {{ $i > 0 ? 'border-t border-dashed border-clay-200' : '' }}">
                <div class="w-10 h-10 rounded-full bg-sage-200 flex items-center justify-center font-serif text-[16px] text-sage-700 shrink-0">
                    {{ mb_substr($user->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-[160px]">
                    <div class="font-medium text-[15px] text-ink-900">{{ $user->name }}</div>
                    <div class="text-[12px] text-ink-700/60">{{ $user->email }}</div>
                </div>
                <div class="w-40 text-[13px] text-ink-700/75 hidden sm:block">
                    {{ $user->role->label() }}
                </div>
                <div class="w-28 text-[13px] text-ink-900">
                    ₺{{ number_format((float) $user->total_donated, 0, ',', '.') }}
                </div>
                <div class="w-24 text-[13px] text-ink-700/75">
                    @if($user->badge_level > 0)
                        🏅 Sv. {{ $user->badge_level }}
                    @else
                        <span class="text-ink-700/40">—</span>
                    @endif
                </div>
                <div class="w-24">
                    @if($user->is_banned)
                        <span class="inline-block rounded-full bg-peach-100 text-ink-900 px-3 py-1 text-[12px] font-medium">Banlı</span>
                    @else
                        <span class="inline-block rounded-full bg-sage-100 text-sage-700 px-3 py-1 text-[12px] font-medium">Aktif</span>
                    @endif
                </div>
                <div class="w-32 text-right">
                    @if($user->id === $currentUserId)
                        <span class="text-[12px] font-hand text-clay-500">bu sizsiniz</span>
                    @elseif($user->is_banned)
                        <button wire:click="unban({{ $user->id }})"
                                wire:confirm="“{{ $user->name }}” kullanıcısının banını kaldırmak istediğinize emin misiniz?"
                                class="rounded-full bg-sage-600 hover:bg-sage-700 text-cream-50 px-4 py-2 text-[13px] font-medium">
                            Banı Kaldır
                        </button>
                    @else
                        <button wire:click="ban({{ $user->id }})"
                                wire:confirm="“{{ $user->name }}” kullanıcısını banlamak istediğinize emin misiniz?"
                                class="rounded-full bg-peach-400 hover:bg-peach-300 text-ink-900 px-4 py-2 text-[13px] font-medium">
                            Banla
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-12 text-center font-hand text-[24px] text-clay-500">
                bu aramayla eşleşen kullanıcı yok 🌱
            </div>
        @endforelse
    </div>
</div>
