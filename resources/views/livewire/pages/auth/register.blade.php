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

<div class="flex flex-col lg:flex-row w-full min-h-screen font-sans text-ink-800">

    <!-- SOL TARAF -->
    <div class="hidden lg:flex flex-col justify-between w-1/2 p-12 bg-cream-50 relative overflow-hidden">
        <!-- Arka Plan Dekorasyonları -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-clay-100/40 rounded-full blur-3xl opacity-60 pointer-events-none"></div>
        <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-sage-100/30 rounded-full blur-3xl opacity-60 pointer-events-none"></div>
        <div class="absolute top-1/3 left-1/2 w-64 h-64 bg-sun-100/20 rounded-full blur-3xl opacity-40 pointer-events-none"></div>

        <!-- Logo -->
        <a href="/" wire:navigate class="flex items-center gap-2 relative z-10 transition-transform hover:scale-105 duration-300">
            <svg class="w-8 h-8 text-sage-700"><use href="#brand-mark"/></svg>
            <span class="font-serif text-2xl text-ink-900 tracking-tight">Re<span class="text-sun-400">·</span>Life</span>
        </a>

        <!-- Dekoratif ikon -->
        <svg class="absolute top-12 right-12 w-24 h-24 text-cream-200/60 rotate-[15deg] transition-transform hover:rotate-45 duration-700"><use href="#brand-mark"/></svg>

        <!-- Ana Metin -->
        <div class="max-w-md relative z-10 mt-10">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-sage-100/60 border border-sage-200/50 mb-6">
                <span class="w-1.5 h-1.5 rounded-full bg-sage-500 animate-pulse"></span>
                <span class="text-[11px] font-semibold text-sage-700 tracking-widest uppercase">Yeni Üyelik</span>
            </div>
            <h1 class="font-serif text-[52px] leading-[1.05] text-ink-900 tracking-tight">
                Bir dokunuşla <span class="italic font-hand text-[60px] text-clay-500">hayat</span>
                <br>değişir.
            </h1>
            <p class="mt-6 text-[15px] text-ink-700/75 leading-relaxed max-w-sm">
                Hayvan sahibi olmak için evinize almanız gerekmiyor. Bir bağışla, bir takiple ya da bir barınağı büyüterek onların kahramanı olabilirsin.
            </p>

            <!-- İstatistik Chips -->
            <div class="flex flex-wrap gap-3 mt-8">
                <div class="flex items-center gap-2 bg-white/80 border border-cream-200 rounded-2xl px-4 py-2.5 shadow-sm">
                    <span class="text-[22px] font-serif text-sage-700">12</span>
                    <span class="text-[11px] text-ink-700/60 leading-tight">aktif<br>barınak</span>
                </div>
                <div class="flex items-center gap-2 bg-white/80 border border-cream-200 rounded-2xl px-4 py-2.5 shadow-sm">
                    <span class="text-[22px] font-serif text-clay-600">340+</span>
                    <span class="text-[11px] text-ink-700/60 leading-tight">iyileşen<br>can</span>
                </div>
                <div class="flex items-center gap-2 bg-white/80 border border-cream-200 rounded-2xl px-4 py-2.5 shadow-sm">
                    <span class="text-[22px] font-serif text-[#a88d3e]">₺2.4M</span>
                    <span class="text-[11px] text-ink-700/60 leading-tight">toplam<br>destek</span>
                </div>
            </div>
        </div>

        <!-- Polaroid Kartlar -->
        <div class="relative mt-12 h-56 z-10">
            <div class="absolute left-0 top-4 w-40 h-44 bg-[#f6f2e8] rounded-xl shadow border border-cream-200 p-2.5 transform -rotate-[6deg] transition-all duration-500 hover:-translate-y-3 hover:rotate-0 hover:shadow-xl hover:z-30 cursor-pointer">
                <div class="w-full h-28 rounded-sm bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1591769225440-811ad7d62ca2?q=80&w=400&auto=format&fit=crop');"></div>
                <div class="mt-2 text-center font-hand text-xs text-ink-700 tracking-widest uppercase">birlikte</div>
            </div>
            <div class="absolute left-32 top-0 w-44 h-48 bg-[#fcf5de] rounded-xl shadow-md border border-[#f3e6be] p-3 transform rotate-[3deg] z-20 transition-all duration-500 hover:-translate-y-3 hover:rotate-0 hover:shadow-2xl hover:z-40 cursor-pointer">
                <div class="w-full h-28 rounded-sm bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1548199973-03cce0bbc87b?q=80&w=400&auto=format&fit=crop');"></div>
                <div class="mt-2 text-center font-hand text-xs text-ink-800 tracking-widest uppercase">güçlüyüz</div>
            </div>
            <div class="absolute left-[270px] top-8 w-40 h-44 bg-[#e9f0e6] rounded-xl shadow border border-[#d6e2d1] p-2.5 transform rotate-[7deg] transition-all duration-500 hover:-translate-y-3 hover:rotate-0 hover:shadow-xl hover:z-30 cursor-pointer">
                <div class="w-full h-28 rounded-sm bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1535268647677-300dbf3d78d1?q=80&w=400&auto=format&fit=crop');"></div>
                <div class="mt-2 text-center font-hand text-xs text-ink-700 tracking-widest uppercase">umutla</div>
            </div>
        </div>

        <!-- Alt Footer -->
        <div class="flex items-end justify-between relative z-10">
            <div class="w-8 h-12 bg-sage-300/40 rounded-t-full rounded-br-full rounded-bl-sm transform -rotate-12"></div>
            <div class="text-[10px] text-ink-900/30 tracking-widest uppercase mb-1">RE·LIFE · SICAK BİR KUCAK İÇİN · MMXXVI</div>
        </div>
    </div>

    <!-- SAĞ TARAF (Form) -->
    <div class="w-full lg:w-1/2 flex items-start justify-center bg-cream-100 px-6 sm:px-10 py-12 relative overflow-y-auto">
        <div class="w-full max-w-[440px]">

            <!-- Başlık -->
            <div class="mb-8">
                <div class="text-[11px] font-semibold text-ink-900/40 tracking-[0.2em] uppercase mb-2">Yeni Hesap</div>
                <h2 class="font-serif text-[38px] leading-[1.1] text-ink-900">Aramıza katıl.</h2>
            </div>

            <!-- ROL SEÇİCİ -->
            <div class="mb-7">
                <div class="text-[10px] font-semibold tracking-[0.15em] uppercase text-ink-900/35 mb-3">Hesap Türü</div>
                <div class="grid grid-cols-3 gap-2.5">

                    <!-- İyileştirici Dost -->
                    <button type="button" wire:click="setRole('user')"
                        class="group relative rounded-2xl border p-4 text-left transition-all duration-300 overflow-hidden
                        {{ $role === 'user'
                            ? 'bg-sage-700 border-sage-700 shadow-lg shadow-sage-700/20'
                            : 'bg-white border-cream-200 hover:border-sage-300 hover:shadow-md' }}">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center mb-3 transition-colors
                            {{ $role === 'user' ? 'bg-white/20' : 'bg-cream-100 group-hover:bg-sage-50' }}">
                            <svg class="w-3.5 h-3.5 {{ $role === 'user' ? 'text-white' : 'text-sage-600' }}"><use href="#heart"/></svg>
                        </div>
                        <div class="text-[11.5px] font-bold leading-tight {{ $role === 'user' ? 'text-white' : 'text-ink-900' }}">İyileştirici<br>Dost</div>
                        @if($role === 'user')
                            <div class="absolute bottom-2 right-2 w-2 h-2 rounded-full bg-sun-300"></div>
                        @endif
                    </button>

                    <!-- Barınak Sahibi -->
                    <button type="button" wire:click="setRole('admin')"
                        class="group relative rounded-2xl border p-4 text-left transition-all duration-300 overflow-hidden
                        {{ $role === 'admin'
                            ? 'bg-clay-600 border-clay-600 shadow-lg shadow-clay-600/20'
                            : 'bg-white border-cream-200 hover:border-clay-300 hover:shadow-md' }}">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center mb-3 transition-colors
                            {{ $role === 'admin' ? 'bg-white/20' : 'bg-cream-100 group-hover:bg-clay-50' }}">
                            <svg class="w-3.5 h-3.5 {{ $role === 'admin' ? 'text-white' : 'text-clay-600' }}"><use href="#leaf"/></svg>
                        </div>
                        <div class="text-[11.5px] font-bold leading-tight {{ $role === 'admin' ? 'text-white' : 'text-ink-900' }}">Barınak<br>Sahibi</div>
                        @if($role === 'admin')
                            <div class="absolute bottom-2 right-2 w-2 h-2 rounded-full bg-sun-300"></div>
                        @endif
                    </button>

                    <!-- Veteriner -->
                    <button type="button" wire:click="setRole('veterinarian')"
                        class="group relative rounded-2xl border p-4 text-left transition-all duration-300 overflow-hidden
                        {{ $role === 'veterinarian'
                            ? 'bg-[#7a6a2e] border-[#7a6a2e] shadow-lg shadow-[#7a6a2e]/20'
                            : 'bg-white border-cream-200 hover:border-[#c4a94a] hover:shadow-md' }}">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center mb-3 transition-colors
                            {{ $role === 'veterinarian' ? 'bg-white/20' : 'bg-cream-100 group-hover:bg-[#fdf5d9]' }}">
                            <svg class="w-3.5 h-3.5 {{ $role === 'veterinarian' ? 'text-white' : 'text-[#9a7e30]' }}"><use href="#brand-mark"/></svg>
                        </div>
                        <div class="text-[11.5px] font-bold leading-tight {{ $role === 'veterinarian' ? 'text-white' : 'text-ink-900' }}">Veteriner</div>
                        @if($role === 'veterinarian')
                            <div class="absolute bottom-2 right-2 w-2 h-2 rounded-full bg-sun-300"></div>
                        @endif
                    </button>
                </div>

                <!-- Rol Açıklaması -->
                <div class="mt-3 px-1">
                    @if($role === 'user')
                        <p class="text-[12px] text-ink-700/60 leading-snug">Bağış yaparak veya hikayeleri takip ederek hayvanlara destek olun.</p>
                    @elseif($role === 'admin')
                        <p class="text-[12px] text-ink-700/60 leading-snug">Barınağınızı platforma ekleyin, hayvanları yönetin ve destek toplayın. <span class="font-semibold text-clay-600">Onay gerektirir.</span></p>
                    @else
                        <p class="text-[12px] text-ink-700/60 leading-snug">Veteriner kimliğinizle barınaklara uzaktan destek verin, sağlık notları düşün.</p>
                    @endif
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

