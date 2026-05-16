<div>
    <h1 class="font-modern text-[32px] text-ink-900 leading-tight">Barınağını kaydet</h1>
    <p class="text-[13.5px] text-ink-700/70 mt-1">
        Başvurun platform yöneticisi tarafından incelenip onaylanacak. Onay sonrası panele giriş yapabilirsin.
    </p>

    <form wire:submit="register" class="space-y-3 mt-5">
        <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500">Yönetici bilgileri</div>
        <div>
            <input type="text" wire:model="name" placeholder="Ad Soyad"
                   class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400" />
            @error('name') <div class="text-[12px] text-clay-600 mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
            <input type="email" wire:model="email" placeholder="E-posta"
                   class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400" />
            @error('email') <div class="text-[12px] text-clay-600 mt-1">{{ $message }}</div> @enderror
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <input type="password" wire:model="password" placeholder="Şifre"
                       class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400" />
                @error('password') <div class="text-[12px] text-clay-600 mt-1">{{ $message }}</div> @enderror
            </div>
            <input type="password" wire:model="password_confirmation" placeholder="Şifre (tekrar)"
                   class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400" />
        </div>

        <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500 pt-2">Barınak bilgileri</div>
        <div>
            <input type="text" wire:model="shelter_name" placeholder="Barınak adı"
                   class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400" />
            @error('shelter_name') <div class="text-[12px] text-clay-600 mt-1">{{ $message }}</div> @enderror
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <input type="text" wire:model="license_no" placeholder="Ruhsat no"
                       class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400" />
                @error('license_no') <div class="text-[12px] text-clay-600 mt-1">{{ $message }}</div> @enderror
            </div>
            <div>
                <input type="text" wire:model="city" placeholder="Şehir"
                       class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400" />
                @error('city') <div class="text-[12px] text-clay-600 mt-1">{{ $message }}</div> @enderror
            </div>
        </div>
        <div>
            <input type="text" wire:model="phone" placeholder="Telefon"
                   class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400" />
            @error('phone') <div class="text-[12px] text-clay-600 mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
            <textarea wire:model="address" placeholder="Açık adres" rows="2"
                      class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400"></textarea>
            @error('address') <div class="text-[12px] text-clay-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="w-full rounded-full bg-sage-600 hover:bg-sage-700 text-cream-50 py-3 text-[14px] font-medium mt-2">
            Başvuruyu Gönder
        </button>
    </form>

    <div class="text-[13px] text-ink-700/65 mt-4 text-center">
        Bağışçı mısın? <a href="{{ route('register') }}" wire:navigate class="text-sage-700 hover:text-sage-600">Buradan kaydol</a>
    </div>
</div>
