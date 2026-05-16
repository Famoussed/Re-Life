<?php

use App\Models\Account\User;
use App\Enums\Account\Role;
use App\Services\Shelter\RegisterShelterAdminService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth-split')] class extends Component
{
    public string $role = 'user'; // user, admin (shelter), veterinarian

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Shelter Fields
    public string $shelter_name = '';
    public string $license_no = '';
    public string $city = '';
    public string $phone = '';
    public string $address = '';

    // Veterinarian Fields
    public string $clinic_name = '';
    public string $diploma_no = '';

    public function setRole(string $role): void
    {
        $this->role = $role;
        $this->resetErrorBag();
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(RegisterShelterAdminService $shelterService): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ];

        if ($this->role === 'admin') {
            $rules = array_merge($rules, [
                'shelter_name' => ['required', 'string', 'max:255'],
                'license_no' => ['required', 'string', 'max:255', 'unique:shelters,license_no'],
                'city' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:255'],
                'address' => ['required', 'string', 'max:500'],
            ]);
        }

        if ($this->role === 'veterinarian') {
            $rules = array_merge($rules, [
                'clinic_name' => ['required', 'string', 'max:255'],
                'diploma_no' => ['required', 'string', 'max:255'],
            ]);
        }

        $validated = $this->validate($rules);

        if ($this->role === 'admin') {
            $user = $shelterService->register($validated);
            session()->flash('status', 'Barınak başvurunuz alındı. Onay sonrası giriş yapabilirsiniz.');
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $this->role === 'veterinarian' ? Role::Veterinarian->value : Role::User->value,
        ];

        event(new Registered($user = User::create($userData)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex flex-col lg:flex-row w-full h-screen font-sans text-ink-800 overflow-hidden">

    <!-- SOL TARAF -->
    <div wire:ignore class="hidden lg:flex flex-col justify-between w-1/2 h-full p-12 bg-cream-50 relative overflow-hidden">
        <!-- Arka Plan Dekorasyonları -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-clay-100/40 rounded-full blur-3xl opacity-60 pointer-events-none"></div>
        <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-sage-100/30 rounded-full blur-3xl opacity-60 pointer-events-none"></div>
        <div class="absolute top-1/3 left-1/2 w-64 h-64 bg-sun-100/20 rounded-full blur-3xl opacity-40 pointer-events-none"></div>

        <!-- Logo -->
        <a href="/" wire:navigate class="flex items-center gap-2 relative z-10 transition-transform hover:scale-105 duration-300">
            <svg class="w-8 h-8 text-sage-700"><use href="#brand-mark"/></svg>
            <span class="font-modern text-2xl text-ink-900 tracking-tight">Re<span class="text-sun-400">·</span>Life</span>
        </a>

        <!-- Dekoratif ikon -->
        <svg class="absolute top-12 right-12 w-24 h-24 text-cream-200/60 rotate-[15deg] transition-transform hover:rotate-45 duration-700"><use href="#brand-mark"/></svg>

        <!-- Ana Metin -->
        <div class="max-w-md relative z-10 mt-10">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-sage-100/60 border border-sage-200/50 mb-6">
                <span class="w-1.5 h-1.5 rounded-full bg-sage-500 animate-pulse"></span>
                <span class="text-[11px] font-semibold text-sage-700 tracking-widest uppercase">Yeni Üyelik</span>
            </div>
            <h1 class="font-modern text-[52px] leading-[1.05] text-ink-900 tracking-tight">
                Bir dokunuşla <span class="italic font-modern text-[60px] text-clay-500">hayat</span>
                <br>değişir.
            </h1>
            <p class="mt-6 text-[15px] text-ink-700/75 leading-relaxed max-w-sm">
                Hayvan sahibi olmak için evinize almanız gerekmiyor. Bir bağışla, bir takiple ya da bir barınağı büyüterek onların kahramanı olabilirsin.
            </p>

            <!-- İstatistik Chips -->
            <div class="flex flex-wrap gap-3 mt-8">
                <div class="flex items-center gap-2 bg-white/80 border border-cream-200 rounded-2xl px-4 py-2.5 shadow-sm">
                    <span class="text-[22px] font-modern text-sage-700">12</span>
                    <span class="text-[11px] text-ink-700/60 leading-tight">aktif<br>barınak</span>
                </div>
                <div class="flex items-center gap-2 bg-white/80 border border-cream-200 rounded-2xl px-4 py-2.5 shadow-sm">
                    <span class="text-[22px] font-modern text-clay-600">340+</span>
                    <span class="text-[11px] text-ink-700/60 leading-tight">iyileşen<br>can</span>
                </div>
                <div class="flex items-center gap-2 bg-white/80 border border-cream-200 rounded-2xl px-4 py-2.5 shadow-sm">
                    <span class="text-[22px] font-modern text-[#a88d3e]">₺2.4M</span>
                    <span class="text-[11px] text-ink-700/60 leading-tight">toplam<br>destek</span>
                </div>
            </div>
        </div>

        <!-- Polaroid Kartlar -->
        <div class="relative mt-12 h-56 z-10">
            <div class="absolute left-0 top-4 w-40 h-44 bg-[#f6f2e8] rounded-xl shadow border border-cream-200 p-2.5 transform -rotate-[6deg] transition-all duration-500 hover:-translate-y-3 hover:rotate-0 hover:shadow-xl hover:z-30 cursor-pointer">
                <div class="w-full h-28 rounded-sm bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1591769225440-811ad7d62ca2?q=80&w=400&auto=format&fit=crop');"></div>
                <div class="mt-2 text-center font-modern text-xs text-ink-700 tracking-widest uppercase">birlikte</div>
            </div>
            <div class="absolute left-32 top-0 w-44 h-48 bg-[#fcf5de] rounded-xl shadow-md border border-[#f3e6be] p-3 transform rotate-[3deg] z-20 transition-all duration-500 hover:-translate-y-3 hover:rotate-0 hover:shadow-2xl hover:z-40 cursor-pointer">
                <div class="w-full h-28 rounded-sm bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1548199973-03cce0bbc87b?q=80&w=400&auto=format&fit=crop');"></div>
                <div class="mt-2 text-center font-modern text-xs text-ink-800 tracking-widest uppercase">güçlüyüz</div>
            </div>
            <div class="absolute left-[270px] top-8 w-40 h-44 bg-[#e9f0e6] rounded-xl shadow border border-[#d6e2d1] p-2.5 transform rotate-[7deg] transition-all duration-500 hover:-translate-y-3 hover:rotate-0 hover:shadow-xl hover:z-30 cursor-pointer">
                <div class="w-full h-28 rounded-sm bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1535268647677-300dbf3d78d1?q=80&w=400&auto=format&fit=crop');"></div>
                <div class="mt-2 text-center font-modern text-xs text-ink-700 tracking-widest uppercase">umutla</div>
            </div>
        </div>

        <!-- Alt Footer -->
        <div class="flex items-end justify-between relative z-10">
            <div class="w-8 h-12 bg-sage-300/40 rounded-t-full rounded-br-full rounded-bl-sm transform -rotate-12"></div>
            <div class="text-[10px] text-ink-900/30 tracking-widest uppercase mb-1">RE·LIFE · SICAK BİR KUCAK İÇİN · MMXXVI</div>
        </div>
    </div>

    <!-- SAĞ TARAF (Form) -->
    <div class="w-full lg:w-1/2 h-full overflow-y-auto flex items-start justify-center bg-cream-100 px-6 sm:px-10 py-12 relative">
        <div class="w-full max-w-[440px]">

            <!-- Başlık -->
            <div class="mb-8">
                <div class="text-[11px] font-semibold text-ink-900/40 tracking-[0.2em] uppercase mb-3">Yeni Hesap</div>
                <h2 class="font-modern text-[38px] leading-[1.1] text-ink-900">Aramıza katıl.</h2>
                <p class="mt-2 text-[13.5px] text-ink-700/55 leading-relaxed">Rolünü seç ve bilgilerini gir — hayvanlara dokunan büyük bir ailenin parçası ol.</p>
            </div>

            <!-- ROL SEÇİCİ -->
            <div class="mb-7">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-[10px] font-semibold tracking-[0.15em] uppercase text-ink-900/40">Hesap Türünü Seç</span>
                    <div class="flex-1 h-px bg-cream-300/60"></div>
                </div>
                <div class="grid grid-cols-3 gap-2.5">

                    <!-- İyileştirici Dost -->
                    <button type="button" wire:click="setRole('user')"
                        class="group relative rounded-2xl border p-4 text-left transition-all duration-300 overflow-hidden
                        {{ $role === 'user'
                            ? 'bg-white border-sage-500 ring-2 ring-sage-500/20 shadow-md'
                            : 'bg-white border-cream-200 hover:border-sage-300 hover:shadow-sm' }}">
                        <!-- İkon -->
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center mb-3 transition-all duration-300
                            {{ $role === 'user' ? 'bg-sage-600' : 'bg-sage-50 group-hover:bg-sage-100' }}">
                            <svg class="w-4 h-4 {{ $role === 'user' ? 'text-white' : 'text-sage-600' }}"><use href="#heart"/></svg>
                        </div>
                        <!-- Başlık -->
                        <div class="text-[12px] font-bold leading-tight mb-1 text-ink-900">İyileştirici<br>Dost</div>
                        <!-- Alt açıklama -->
                        <div class="text-[9.5px] leading-snug text-ink-700/45">Bağış yap &amp; takip et</div>
                        <!-- Seçim çizgisi -->
                        <div class="absolute bottom-0 left-0 right-0 h-0.5 rounded-b-2xl transition-all duration-300 {{ $role === 'user' ? 'bg-sage-500' : 'bg-transparent' }}"></div>
                    </button>

                    <!-- Barınak Sahibi -->
                    <button type="button" wire:click="setRole('admin')"
                        class="group relative rounded-2xl border p-4 text-left transition-all duration-300 overflow-hidden
                        {{ $role === 'admin'
                            ? 'bg-white border-clay-500 ring-2 ring-clay-500/20 shadow-md'
                            : 'bg-white border-cream-200 hover:border-clay-300 hover:shadow-sm' }}">
                        <!-- İkon -->
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center mb-3 transition-all duration-300
                            {{ $role === 'admin' ? 'bg-clay-600' : 'bg-clay-50 group-hover:bg-clay-100' }}">
                            <svg class="w-4 h-4 {{ $role === 'admin' ? 'text-white' : 'text-clay-600' }}"><use href="#leaf"/></svg>
                        </div>
                        <!-- Başlık -->
                        <div class="text-[12px] font-bold leading-tight mb-1 text-ink-900">Barınak<br>Sahibi</div>
                        <!-- Alt açıklama -->
                        <div class="text-[9.5px] leading-snug text-ink-700/45">Barınağını yönet</div>
                        <!-- Seçim çizgisi -->
                        <div class="absolute bottom-0 left-0 right-0 h-0.5 rounded-b-2xl transition-all duration-300 {{ $role === 'admin' ? 'bg-clay-500' : 'bg-transparent' }}"></div>
                    </button>

                    <!-- Veteriner -->
                    <button type="button" wire:click="setRole('veterinarian')"
                        class="group relative rounded-2xl border p-4 text-left transition-all duration-300 overflow-hidden
                        {{ $role === 'veterinarian'
                            ? 'bg-white border-[#a88d3e] ring-2 ring-[#a88d3e]/20 shadow-md'
                            : 'bg-white border-cream-200 hover:border-[#c4a94a] hover:shadow-sm' }}">
                        <!-- İkon -->
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center mb-3 transition-all duration-300
                            {{ $role === 'veterinarian' ? 'bg-[#a88d3e]' : 'bg-[#fdf5d9] group-hover:bg-[#f7ebb0]' }}">
                            <svg class="w-4 h-4 {{ $role === 'veterinarian' ? 'text-white' : 'text-[#9a7e30]' }}"><use href="#brand-mark"/></svg>
                        </div>
                        <!-- Başlık -->
                        <div class="text-[12px] font-bold leading-tight mb-1 text-ink-900">Veteriner</div>
                        <!-- Alt açıklama -->
                        <div class="text-[9.5px] leading-snug text-ink-700/45">Sağlık notu düş</div>
                        <!-- Seçim çizgisi -->
                        <div class="absolute bottom-0 left-0 right-0 h-0.5 rounded-b-2xl transition-all duration-300 {{ $role === 'veterinarian' ? 'bg-[#a88d3e]' : 'bg-transparent' }}"></div>
                    </button>
                </div>

                <!-- Rol Açıklaması (Dinamik) -->
                <div class="mt-4 flex items-start gap-3 p-3.5 rounded-2xl transition-all duration-300
                    {{ $role === 'user' ? 'bg-sage-50/70 border border-sage-100' : ($role === 'admin' ? 'bg-clay-50/70 border border-clay-100' : 'bg-[#fdf8e8]/70 border border-[#e8d88a]/40') }}">
                    <div class="mt-0.5 shrink-0">
                        @if($role === 'user')
                            <div class="w-5 h-5 rounded-full bg-sage-200 flex items-center justify-center">
                                <svg class="w-2.5 h-2.5 text-sage-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        @elseif($role === 'admin')
                            <div class="w-5 h-5 rounded-full bg-clay-200 flex items-center justify-center">
                                <svg class="w-2.5 h-2.5 text-clay-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        @else
                            <div class="w-5 h-5 rounded-full bg-[#e8d88a]/60 flex items-center justify-center">
                                <svg class="w-2.5 h-2.5 text-[#7a6a2e]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        @endif
                    </div>
                    <p class="text-[12px] leading-relaxed
                        {{ $role === 'user' ? 'text-sage-800/80' : ($role === 'admin' ? 'text-clay-800/80' : 'text-[#5a4a1e]/80') }}">
                        @if($role === 'user')
                            Bağış yaparak veya hikayeleri takip ederek hayvanlara destek olun. Hemen ücretsiz başlayabilirsiniz.
                        @elseif($role === 'admin')
                            Barınağınızı platforma ekleyin ve destek toplayın. <strong class="font-semibold">Platform yöneticisinin onayı gerektirir.</strong>
                        @else
                            Veteriner kimliğinizle barınaklara uzaktan destek verin, sağlık notları düşün ve takip edin.
                        @endif
                    </p>
                </div>
            </div>

            <!-- FORM -->
            <form wire:submit="register" class="space-y-4">

                <!-- Temel Bilgiler -->
                <div class="space-y-3">
                    <div class="text-[10px] font-semibold tracking-[0.15em] uppercase text-ink-900/35">Kişisel Bilgiler</div>

                    <div class="relative">
                        <input wire:model="name" type="text" required autofocus placeholder="Ad Soyad"
                            class="w-full bg-white border border-cream-200 rounded-2xl px-5 py-3.5 text-[14px] text-ink-900 placeholder:text-ink-400/60
                            focus:outline-none focus:ring-2 focus:ring-sage-500/25 focus:border-sage-400
                            transition-all shadow-[0_1px_4px_rgba(0,0,0,0.03)] hover:shadow-sm
                            {{ $errors->has('name') ? 'border-red-300 focus:ring-red-400/25 focus:border-red-400' : '' }}">
                        @error('name') <p class="mt-1.5 text-[11px] text-red-500 pl-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="relative">
                        <input wire:model="email" type="email" required placeholder="E-Posta"
                            class="w-full bg-white border border-cream-200 rounded-2xl px-5 py-3.5 text-[14px] text-ink-900 placeholder:text-ink-400/60
                            focus:outline-none focus:ring-2 focus:ring-sage-500/25 focus:border-sage-400
                            transition-all shadow-[0_1px_4px_rgba(0,0,0,0.03)] hover:shadow-sm
                            {{ $errors->has('email') ? 'border-red-300 focus:ring-red-400/25 focus:border-red-400' : '' }}">
                        @error('email') <p class="mt-1.5 text-[11px] text-red-500 pl-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- BARINAK BİLGİLERİ -->
                @if($role === 'admin')
                <div class="space-y-3 pt-1">
                    <div class="flex items-center gap-2">
                        <div class="text-[10px] font-semibold tracking-[0.15em] uppercase text-clay-600">Barınak Bilgileri</div>
                        <div class="flex-1 h-px bg-clay-200/50"></div>
                    </div>

                    <input wire:model="shelter_name" type="text" placeholder="Barınak Adı"
                        class="w-full bg-white border border-cream-200 rounded-2xl px-5 py-3.5 text-[14px] placeholder:text-ink-400/60 focus:outline-none focus:ring-2 focus:ring-clay-400/25 focus:border-clay-400 transition-all shadow-[0_1px_4px_rgba(0,0,0,0.03)]
                        {{ $errors->has('shelter_name') ? 'border-red-300' : '' }}">
                    @error('shelter_name') <p class="mt-1 text-[11px] text-red-500 pl-1">{{ $message }}</p> @enderror

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <input wire:model="license_no" type="text" placeholder="Ruhsat No"
                                class="w-full bg-white border border-cream-200 rounded-2xl px-5 py-3.5 text-[14px] placeholder:text-ink-400/60 focus:outline-none focus:ring-2 focus:ring-clay-400/25 focus:border-clay-400 transition-all shadow-[0_1px_4px_rgba(0,0,0,0.03)]">
                            @error('license_no') <p class="mt-1 text-[11px] text-red-500 pl-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <input wire:model="city" type="text" placeholder="Şehir"
                                class="w-full bg-white border border-cream-200 rounded-2xl px-5 py-3.5 text-[14px] placeholder:text-ink-400/60 focus:outline-none focus:ring-2 focus:ring-clay-400/25 focus:border-clay-400 transition-all shadow-[0_1px_4px_rgba(0,0,0,0.03)]">
                            @error('city') <p class="mt-1 text-[11px] text-red-500 pl-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <input wire:model="phone" type="text" placeholder="Telefon Numarası"
                        class="w-full bg-white border border-cream-200 rounded-2xl px-5 py-3.5 text-[14px] placeholder:text-ink-400/60 focus:outline-none focus:ring-2 focus:ring-clay-400/25 focus:border-clay-400 transition-all shadow-[0_1px_4px_rgba(0,0,0,0.03)]">
                    @error('phone') <p class="mt-1 text-[11px] text-red-500 pl-1">{{ $message }}</p> @enderror

                    <textarea wire:model="address" placeholder="Açık Adres" rows="2"
                        class="w-full bg-white border border-cream-200 rounded-2xl px-5 py-3 text-[14px] placeholder:text-ink-400/60 focus:outline-none focus:ring-2 focus:ring-clay-400/25 focus:border-clay-400 transition-all shadow-[0_1px_4px_rgba(0,0,0,0.03)] resize-none"></textarea>
                    @error('address') <p class="mt-1 text-[11px] text-red-500 pl-1">{{ $message }}</p> @enderror
                </div>
                @endif

                <!-- VETERİNER BİLGİLERİ -->
                @if($role === 'veterinarian')
                <div class="space-y-3 pt-1">
                    <div class="flex items-center gap-2">
                        <div class="text-[10px] font-semibold tracking-[0.15em] uppercase text-[#7a6a2e]">Mesleki Bilgiler</div>
                        <div class="flex-1 h-px bg-[#d4c07a]/40"></div>
                    </div>

                    <input wire:model="clinic_name" type="text" placeholder="Klinik / Hastane Adı"
                        class="w-full bg-white border border-cream-200 rounded-2xl px-5 py-3.5 text-[14px] placeholder:text-ink-400/60 focus:outline-none focus:ring-2 focus:ring-[#a88d3e]/25 focus:border-[#a88d3e] transition-all shadow-[0_1px_4px_rgba(0,0,0,0.03)]">
                    @error('clinic_name') <p class="mt-1 text-[11px] text-red-500 pl-1">{{ $message }}</p> @enderror

                    <input wire:model="diploma_no" type="text" placeholder="Diploma / Sicil Numarası"
                        class="w-full bg-white border border-cream-200 rounded-2xl px-5 py-3.5 text-[14px] placeholder:text-ink-400/60 focus:outline-none focus:ring-2 focus:ring-[#a88d3e]/25 focus:border-[#a88d3e] transition-all shadow-[0_1px_4px_rgba(0,0,0,0.03)]">
                    @error('diploma_no') <p class="mt-1 text-[11px] text-red-500 pl-1">{{ $message }}</p> @enderror
                </div>
                @endif

                <!-- ŞİFRE -->
                <div class="space-y-3 pt-1">
                    <div class="text-[10px] font-semibold tracking-[0.15em] uppercase text-ink-900/35">Şifre</div>
                    <div class="relative">
                        <input wire:model="password" type="password" required placeholder="Şifre belirle"
                            class="w-full bg-white border border-cream-200 rounded-2xl px-5 py-3.5 text-[14px] placeholder:text-ink-400/60
                            focus:outline-none focus:ring-2 focus:ring-sage-500/25 focus:border-sage-400
                            transition-all shadow-[0_1px_4px_rgba(0,0,0,0.03)] hover:shadow-sm
                            {{ $errors->has('password') ? 'border-red-300 focus:ring-red-400/25 focus:border-red-400' : '' }}">
                        @error('password') <p class="mt-1.5 text-[11px] text-red-500 pl-1">{{ $message }}</p> @enderror
                    </div>
                    <input wire:model="password_confirmation" type="password" required placeholder="Şifreyi tekrarla"
                        class="w-full bg-white border border-cream-200 rounded-2xl px-5 py-3.5 text-[14px] placeholder:text-ink-400/60
                        focus:outline-none focus:ring-2 focus:ring-sage-500/25 focus:border-sage-400
                        transition-all shadow-[0_1px_4px_rgba(0,0,0,0.03)] hover:shadow-sm">
                </div>

                <!-- SUBMIT -->
                <div class="pt-3">
                    <button type="submit" wire:loading.attr="disabled" wire:target="register"
                        class="w-full rounded-full py-4 text-[14px] font-semibold transition-all duration-300 flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-wait group
                        {{ $role === 'admin'
                            ? 'bg-clay-600 hover:bg-clay-700 text-white shadow-lg shadow-clay-600/25 hover:shadow-clay-600/40'
                            : ($role === 'veterinarian'
                                ? 'bg-[#7a6a2e] hover:bg-[#6a5a25] text-white shadow-lg shadow-[#7a6a2e]/25 hover:shadow-[#7a6a2e]/40'
                                : 'bg-[#596d53] hover:bg-[#4c5f46] text-[#f7fae8] shadow-lg shadow-[#596d53]/25 hover:shadow-[#596d53]/40') }}">
                        <span wire:loading.remove wire:target="register" class="flex items-center gap-2 transform transition-transform group-hover:translate-x-0.5">
                            @if($role === 'admin')
                                Barınak Başvurusu Gönder
                            @elseif($role === 'veterinarian')
                                Veteriner Hesabı Oluştur
                            @else
                                Hesabımı Oluştur
                            @endif
                            <span>→</span>
                        </span>
                        <span wire:loading wire:target="register" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Oluşturuluyor...
                        </span>
                    </button>
                </div>
            </form>

            <!-- Ayırıcı -->
            <div class="relative my-7">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-cream-300/60"></div>
                </div>
                <div class="relative flex justify-center text-[10px] tracking-widest text-ink-900/30 uppercase">
                    <span class="bg-cream-100 px-3">Zaten üye misin?</span>
                </div>
            </div>

            <!-- Giriş Yap Linki -->
            <a href="{{ route('login') }}" wire:navigate
                class="block w-full text-center rounded-full py-3.5 text-[13.5px] font-medium border border-cream-200 bg-white text-ink-700
                hover:border-sage-300 hover:text-sage-700 hover:shadow-sm transition-all duration-300">
                Giriş Yap
            </a>
        </div>
    </div>
</div>

