<div>
    <header class="flex items-center justify-between gap-4 mb-7">
        <div>
            <h1 class="font-modern text-[34px] leading-tight text-ink-900">İyileşme Güncellemeleri</h1>
            <p class="text-[14px] text-ink-700/65 mt-1">Bir dostun iyileşme yolculuğunu fotoğraflarla paylaş — bağışçılar haberdar olsun.</p>
        </div>
        @unless($showForm)
            <button wire:click="create"
                class="rounded-full bg-sage-600 hover:bg-sage-700 text-cream-50 px-5 py-2.5 text-[14px] font-medium">
                + Yeni Güncelleme
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
            <h2 class="font-modern text-[22px] text-ink-900 mb-4">Yeni İyileşme Güncellemesi</h2>

            @if($animals->isEmpty())
                <p class="text-[14px] text-clay-500">
                    Henüz hayvan eklenmemiş. Önce <a href="{{ route('admin.animals') }}" class="underline">Hayvanlar</a> bölümünden hayvan ekleyin.
                </p>
            @else
                <div class="space-y-4">
                    <div>
                        <label class="block text-[13px] text-ink-700 mb-1">Hayvan</label>
                        <select wire:model="animalId"
                            class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400">
                            <option value="">— Hayvan seçin —</option>
                            @foreach($animals as $animal)
                                <option value="{{ $animal->id }}">{{ $animal->name }}</option>
                            @endforeach
                        </select>
                        @error('animalId') <span class="text-[12px] text-clay-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[13px] text-ink-700 mb-1">Başlık</label>
                        <input type="text" wire:model="title" placeholder="örn: Pati'nin ameliyatı başarılı geçti"
                            class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400">
                        @error('title') <span class="text-[12px] text-clay-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[13px] text-ink-700 mb-1">İyileşme Notu</label>
                        <textarea wire:model="note" rows="5" placeholder="Hayvanın son durumunu, iyileşme sürecini anlat…"
                            class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400"></textarea>
                        @error('note') <span class="text-[12px] text-clay-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[13px] text-ink-700 mb-1">Fotoğraflar <span class="text-ink-700/50">(en fazla 6, her biri 4 MB)</span></label>
                        <input type="file" wire:model="photos" multiple accept="image/*"
                            class="block w-full text-[13px] text-ink-700
                            file:mr-3 file:rounded-full file:border-0 file:bg-sage-100 file:px-4 file:py-2
                            file:text-[13px] file:text-sage-700 hover:file:bg-sage-200">
                        @error('photos') <span class="text-[12px] text-clay-500">{{ $message }}</span> @enderror
                        @error('photos.*') <span class="text-[12px] text-clay-500">{{ $message }}</span> @enderror

                        <div wire:loading wire:target="photos" class="text-[12px] text-ink-700/60 mt-2">
                            Fotoğraflar yükleniyor…
                        </div>

                        {{-- Önizleme --}}
                        @if(!empty($photos))
                            <div class="flex flex-wrap gap-3 mt-3">
                                @foreach($photos as $photo)
                                    @if(is_object($photo) && method_exists($photo, 'temporaryUrl'))
                                        <img src="{{ $photo->temporaryUrl() }}" alt=""
                                            class="w-20 h-20 rounded-xl object-cover border border-cream-300/60">
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-5">
                    <button type="submit" wire:loading.attr="disabled" wire:target="save,photos"
                        class="rounded-full bg-sage-600 hover:bg-sage-700 text-cream-50 px-5 py-2.5 text-[14px] font-medium disabled:opacity-60">
                        <span wire:loading.remove wire:target="save">Yayınla</span>
                        <span wire:loading wire:target="save">Yayınlanıyor…</span>
                    </button>
                    <button type="button" wire:click="cancel"
                        class="rounded-full bg-cream-100 hover:bg-cream-200 text-ink-700 px-5 py-2.5 text-[14px]">
                        Vazgeç
                    </button>
                </div>
            @endif
        </form>
    @endif

    {{-- LİSTE --}}
    @if($updates->isEmpty())
        <div class="paper-card rounded-4xl border border-cream-300/50 p-12 text-center">
            <div class="font-modern text-[24px] text-clay-500">henüz iyileşme güncellemesi paylaşılmamış</div>
        </div>
    @else
        <div class="space-y-4">
            @foreach($updates as $update)
                <article class="paper-note rounded-3xl p-5 shadow-note" wire:key="recovery-{{ $update->id }}">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-[12px] uppercase tracking-[0.14em] text-clay-500">{{ $update->animal->name }}</div>
                            <h3 class="font-modern text-[19px] text-ink-900 mt-0.5">{{ $update->title }}</h3>
                        </div>
                        <span class="text-[12px] text-ink-700/55 shrink-0">{{ $update->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    <p class="text-[14px] text-ink-700/80 mt-2 leading-[1.6] whitespace-pre-line">{{ $update->note }}</p>
                    @if($update->images->isNotEmpty())
                        <div class="flex flex-wrap gap-2 mt-3">
                            @foreach($update->images as $image)
                                <img src="{{ $image->url }}" alt="{{ $update->animal->name }} iyileşme fotoğrafı"
                                    class="w-24 h-24 rounded-xl object-cover border border-cream-300/60">
                            @endforeach
                        </div>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
</div>
