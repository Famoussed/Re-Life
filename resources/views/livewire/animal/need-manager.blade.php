<div>
    <header class="flex items-center justify-between gap-4 mb-7">
        <div>
            <h1 class="font-modern text-[34px] leading-tight text-ink-900">İhtiyaçlar</h1>
            <p class="text-[14px] text-ink-700/65 mt-1">Dostlarının mama, aşı ve tedavi ihtiyaçlarını yönet.</p>
        </div>
        @unless($showForm)
            <button wire:click="create"
                class="rounded-full bg-sage-600 hover:bg-sage-700 text-cream-50 px-5 py-2.5 text-[14px] font-medium">
                + Yeni İhtiyaç
            </button>
        @endunless
    </header>

    @if(session('message'))
        <div class="paper-note rounded-2xl px-4 py-3 mb-5 text-[14px] text-sage-700 shadow-note">
            {{ session('message') }}
        </div>
    @endif

    {{-- INLINE FORM --}}
    @if($showForm)
        <form wire:submit="save" class="paper-card rounded-3xl border border-cream-300/60 p-6 shadow-card mb-7">
            <h2 class="font-modern text-[22px] text-ink-900 mb-4">
                {{ $editingId ? 'İhtiyacı Düzenle' : 'Yeni İhtiyaç' }}
            </h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[13px] text-ink-700 mb-1">Hayvan</label>
                    <select wire:model="animal_id"
                        class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400">
                        <option value="">— seçin —</option>
                        @foreach($animals as $animal)
                            <option value="{{ $animal->id }}">{{ $animal->name }}</option>
                        @endforeach
                    </select>
                    @error('animal_id') <span class="text-[12px] text-clay-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[13px] text-ink-700 mb-1">Tür</label>
                    <select wire:model="type"
                        class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400">
                        <option value="">— seçin —</option>
                        @foreach(\App\Enums\Animal\NeedType::cases() as $t)
                            <option value="{{ $t->value }}">{{ $t->label() }}</option>
                        @endforeach
                    </select>
                    @error('type') <span class="text-[12px] text-clay-500">{{ $message }}</span> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[13px] text-ink-700 mb-1">Başlık</label>
                    <input type="text" wire:model="title"
                        class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400">
                    @error('title') <span class="text-[12px] text-clay-500">{{ $message }}</span> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[13px] text-ink-700 mb-1">Açıklama</label>
                    <textarea wire:model="description" rows="3"
                        class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400"></textarea>
                    @error('description') <span class="text-[12px] text-clay-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[13px] text-ink-700 mb-1">Hedef Tutar (₺)</label>
                    <input type="number" step="0.01" min="1" wire:model="target_amount"
                        class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400">
                    @error('target_amount') <span class="text-[12px] text-clay-500">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="flex items-center gap-3 mt-5">
                <button type="submit"
                    class="rounded-full bg-sage-600 hover:bg-sage-700 text-cream-50 px-5 py-2.5 text-[14px] font-medium">
                    Kaydet
                </button>
                <button type="button" wire:click="cancel"
                    class="rounded-full bg-cream-100 hover:bg-cream-200 text-ink-700 px-5 py-2.5 text-[14px]">
                    Vazgeç
                </button>
            </div>
        </form>
    @endif

    {{-- LİSTE --}}
    @if($needs->isEmpty())
        <div class="paper-card rounded-4xl border border-cream-300/50 p-12 text-center">
            <div class="font-modern text-[24px] text-clay-500">henüz ihtiyaç eklenmemiş</div>
        </div>
    @else
        <div class="space-y-4">
            @foreach($needs as $need)
                <div class="paper-card rounded-3xl border border-cream-300/60 p-5 shadow-card" wire:key="need-{{ $need->id }}">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-modern text-[18px] text-ink-900">{{ $need->title }}</h3>
                                <span class="text-[11px] rounded-full bg-cream-200 text-ink-700/70 px-2 py-0.5">{{ $need->type->label() }}</span>
                                @if($need->status === \App\Enums\Animal\NeedStatus::Active)
                                    <span class="text-[11px] rounded-full bg-sage-100 text-sage-700 px-2 py-0.5">Aktif</span>
                                @elseif($need->status === \App\Enums\Animal\NeedStatus::Completed)
                                    <span class="text-[11px] rounded-full bg-sun-100 text-sun-500 px-2 py-0.5">Tamamlandı ✓</span>
                                @else
                                    <span class="text-[11px] rounded-full bg-clay-50 text-clay-500 px-2 py-0.5">{{ $need->status->label() }}</span>
                                @endif
                            </div>
                            <div class="text-[13px] text-ink-700/70 mt-1 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-sage-600"><use href="#paw" fill="currentColor"/></svg>
                                {{ $need->animal?->name ?? 'Hayvan silinmiş' }}
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($need->status === \App\Enums\Animal\NeedStatus::Active)
                                <button wire:click="edit({{ $need->id }})"
                                    class="rounded-full bg-cream-100 hover:bg-cream-200 text-ink-700 px-4 py-1.5 text-[13px]">
                                    Düzenle
                                </button>
                                <button wire:click="cancelNeed({{ $need->id }})"
                                    wire:confirm="Bu ihtiyacı iptal etmek istediğine emin misin?"
                                    class="rounded-full bg-clay-50 hover:bg-clay-100 text-clay-600 px-4 py-1.5 text-[13px]">
                                    İptal et
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center justify-between text-[13px] text-ink-700/70 mb-1.5">
                            <span>{{ number_format((float) $need->collected_amount, 2, ',', '.') }} ₺ toplandı</span>
                            <span>Hedef: {{ number_format((float) $need->target_amount, 2, ',', '.') }} ₺</span>
                        </div>
                        <x-progress :percent="$need->progressPercent()" />
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
