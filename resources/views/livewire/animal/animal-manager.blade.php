<div>
    <header class="flex items-center justify-between gap-4 mb-7">
        <div>
            <h1 class="font-modern text-[34px] leading-tight text-ink-900">Hayvanlar</h1>
            <p class="text-[14px] text-ink-700/65 mt-1">Barınağındaki dostları ekle, düzenle ve yönet.</p>
        </div>
        @unless($showForm)
            <button wire:click="create"
                class="rounded-full bg-sage-600 hover:bg-sage-700 text-cream-50 px-5 py-2.5 text-[14px] font-medium">
                + Yeni Hayvan
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
                {{ $editingId ? 'Hayvanı Düzenle' : 'Yeni Hayvan' }}
            </h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[13px] text-ink-700 mb-1">Ad</label>
                    <input type="text" wire:model="name"
                        class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400">
                    @error('name') <span class="text-[12px] text-clay-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[13px] text-ink-700 mb-1">Tür</label>
                    <select wire:model="species"
                        class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400">
                        <option value="">— seçin —</option>
                        @foreach(\App\Enums\Animal\AnimalSpecies::cases() as $s)
                            <option value="{{ $s->value }}">{{ $s->label() }}</option>
                        @endforeach
                    </select>
                    @error('species') <span class="text-[12px] text-clay-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[13px] text-ink-700 mb-1">Tahmini Yaş</label>
                    <input type="text" wire:model="age_estimate" placeholder="örn. 2 yaş"
                        class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400">
                    @error('age_estimate') <span class="text-[12px] text-clay-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[13px] text-ink-700 mb-1">Cinsiyet</label>
                    <select wire:model="gender"
                        class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400">
                        <option value="">— seçin —</option>
                        @foreach(\App\Enums\Animal\Gender::cases() as $g)
                            <option value="{{ $g->value }}">{{ $g->label() }}</option>
                        @endforeach
                    </select>
                    @error('gender') <span class="text-[12px] text-clay-500">{{ $message }}</span> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[13px] text-ink-700 mb-1">Hikâyesi</label>
                    <textarea wire:model="story" rows="3"
                        class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400"></textarea>
                    @error('story') <span class="text-[12px] text-clay-500">{{ $message }}</span> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[13px] text-ink-700 mb-1">Sağlık Durumu</label>
                    <textarea wire:model="health_status" rows="2"
                        class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400"></textarea>
                    @error('health_status') <span class="text-[12px] text-clay-500">{{ $message }}</span> @enderror
                </div>
                <label class="flex items-center gap-2 text-[14px] text-ink-700">
                    <input type="checkbox" wire:model="is_active"
                        class="rounded border-cream-300/60 text-sage-600 focus:ring-sage-400">
                    Aktif (sitede gösterilsin)
                </label>
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
    @if($animals->isEmpty())
        <div class="paper-card rounded-4xl border border-cream-300/50 p-12 text-center">
            <div class="font-modern text-[24px] text-clay-500">henüz hayvan eklenmemiş</div>
        </div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($animals as $animal)
                <div class="paper-card rounded-3xl border border-cream-300/60 p-4 shadow-card" wire:key="animal-{{ $animal->id }}">
                    <div class="flex gap-4">
                        <x-photo :path="$animal->photo_path" :label="mb_substr($animal->name, 0, 1)"
                            class="w-20 h-20 rounded-2xl shrink-0" />
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="font-modern text-[18px] text-ink-900 truncate">{{ $animal->name }}</h3>
                                @if($animal->is_active)
                                    <span class="text-[11px] rounded-full bg-sage-100 text-sage-700 px-2 py-0.5">Aktif</span>
                                @else
                                    <span class="text-[11px] rounded-full bg-cream-200 text-ink-700/60 px-2 py-0.5">Pasif</span>
                                @endif
                            </div>
                            <div class="text-[13px] text-ink-700/70 mt-1">{{ $animal->species->label() }}</div>
                            <div class="text-[12px] text-ink-700/55">{{ $animal->age_estimate ?: 'Yaş bilinmiyor' }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mt-4">
                        <button wire:click="edit({{ $animal->id }})"
                            class="rounded-full bg-cream-100 hover:bg-cream-200 text-ink-700 px-4 py-1.5 text-[13px]">
                            Düzenle
                        </button>
                        <button wire:click="delete({{ $animal->id }})"
                            wire:confirm="Bu hayvanı silmek istediğine emin misin?"
                            class="rounded-full bg-clay-50 hover:bg-clay-100 text-clay-600 px-4 py-1.5 text-[13px]">
                            Sil
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
