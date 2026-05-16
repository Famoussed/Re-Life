<div>
    <header class="mb-7">
        <h1 class="font-serif text-[34px] leading-tight text-ink-900">Barınak Profili</h1>
        <p class="text-[14px] text-ink-700/65 mt-1">Barınağının iletişim ve konum bilgilerini güncel tut.</p>
    </header>

    @if(session('message'))
        <div class="paper-note rounded-2xl px-4 py-3 mb-5 text-[14px] text-sage-700 shadow-note">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit="save" class="paper-card rounded-3xl border border-cream-300/60 p-6 shadow-card max-w-2xl">
        <div class="grid sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-[13px] text-ink-700 mb-1">Barınak Adı</label>
                <input type="text" wire:model="name"
                    class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400">
                @error('name') <span class="text-[12px] text-clay-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-[13px] text-ink-700 mb-1">Şehir</label>
                <input type="text" wire:model="city"
                    class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400">
                @error('city') <span class="text-[12px] text-clay-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-[13px] text-ink-700 mb-1">Telefon</label>
                <input type="text" wire:model="phone"
                    class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400">
                @error('phone') <span class="text-[12px] text-clay-500">{{ $message }}</span> @enderror
            </div>
            <div class="sm:col-span-2">
                <label class="block text-[13px] text-ink-700 mb-1">Adres</label>
                <textarea wire:model="address" rows="3"
                    class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400"></textarea>
                @error('address') <span class="text-[12px] text-clay-500">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- SALT OKUNUR ALANLAR --}}
        <div class="grid sm:grid-cols-2 gap-4 mt-4 pt-4 border-t border-cream-300/60">
            <div>
                <label class="block text-[13px] text-ink-700/60 mb-1">Lisans No (değiştirilemez)</label>
                <div class="w-full rounded-2xl bg-cream-200/70 border border-cream-300/60 px-3 py-2.5 text-[14px] text-ink-700/70">
                    {{ $licenseNo ?: '—' }}
                </div>
            </div>
            <div>
                <label class="block text-[13px] text-ink-700/60 mb-1">Durum (değiştirilemez)</label>
                <div class="w-full rounded-2xl bg-cream-200/70 border border-cream-300/60 px-3 py-2.5 text-[14px] text-ink-700/70">
                    {{ $statusLabel }}
                </div>
            </div>
        </div>

        <div class="mt-5">
            <button type="submit"
                class="rounded-full bg-sage-600 hover:bg-sage-700 text-cream-50 px-5 py-2.5 text-[14px] font-medium">
                Kaydet
            </button>
        </div>
    </form>
</div>
