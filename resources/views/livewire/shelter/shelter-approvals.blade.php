<div class="max-w-[1000px]">
    <div class="text-[12px] uppercase tracking-[0.16em] text-clay-500 font-medium">Platform Yönetimi</div>
    <h1 class="font-modern text-[40px] sm:text-[48px] leading-[0.95] text-ink-900 mt-1">Barınak Onayları</h1>
    <p class="text-[15px] text-ink-700/75 mt-2 max-w-[560px]">
        Kayıt başvurusu yapan ve onay bekleyen barınakları inceleyin.
    </p>

    @if(session('status'))
        <div class="paper-note rounded-2xl shadow-note border border-sage-200 mt-5 px-5 py-3 text-[14px] text-sage-700">
            {{ session('status') }}
        </div>
    @endif

    <div class="space-y-4 mt-6">
        @forelse($shelters as $shelter)
            <div class="paper-card rounded-4xl shadow-card border border-cream-300/50 p-5 sm:p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="font-modern text-[22px] text-ink-900">{{ $shelter->name }}</div>
                        <div class="text-[13px] text-ink-700/70 mt-0.5">{{ $shelter->city }}</div>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        <button wire:click="approve({{ $shelter->id }})"
                                wire:confirm="“{{ $shelter->name }}” barınağını onaylamak istediğinize emin misiniz?"
                                class="rounded-full bg-sage-600 hover:bg-sage-700 text-cream-50 px-5 py-2.5 text-[14px] font-medium">
                            Onayla
                        </button>
                        <button wire:click="reject({{ $shelter->id }})"
                                wire:confirm="“{{ $shelter->name }}” barınağını reddetmek istediğinize emin misiniz?"
                                class="rounded-full bg-peach-400 hover:bg-peach-300 text-ink-900 px-5 py-2.5 text-[14px] font-medium">
                            Reddet
                        </button>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-x-8 gap-y-2 mt-4 pt-4 border-t border-dashed border-clay-200 text-[14px]">
                    <div>
                        <span class="text-ink-700/55">Ruhsat No:</span>
                        <span class="text-ink-900">{{ $shelter->license_no }}</span>
                    </div>
                    <div>
                        <span class="text-ink-700/55">Telefon:</span>
                        <span class="text-ink-900">{{ $shelter->phone }}</span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-ink-700/55">Adres:</span>
                        <span class="text-ink-900">{{ $shelter->address }}</span>
                    </div>
                    <div>
                        <span class="text-ink-700/55">Yönetici:</span>
                        <span class="text-ink-900">{{ $shelter->admin?->name ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-ink-700/55">E-posta:</span>
                        <span class="text-ink-900">{{ $shelter->admin?->email ?? '—' }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="paper-card rounded-4xl shadow-card border border-cream-300/50 p-12 text-center">
                <div class="font-modern text-[26px] text-clay-500">onay bekleyen barınak yok 🌿</div>
                <p class="text-[14px] text-ink-700/65 mt-2">Tüm başvurular sonuçlandırılmış.</p>
            </div>
        @endforelse
    </div>
</div>
