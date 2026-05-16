<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth-split')] class extends Component
{
    public LoginForm $form;
    public string $selectedRole = 'donor';

    public function mount(): void
    {
        // Geliştirme ortamı için varsayılan kullanıcı - İPTAL EDİLDİ
    }

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex flex-col lg:flex-row w-full min-h-screen font-sans text-ink-800">
    
    <!-- SOL TARAF (Görsel & Metin) -->
    <div class="hidden lg:flex flex-col justify-between w-1/2 p-12 bg-cream-50 relative overflow-hidden">
        <!-- Arka Plan Dekorasyonları -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-sage-100/50 rounded-full blur-3xl opacity-60 pointer-events-none"></div>
        <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-sun-100/30 rounded-full blur-3xl opacity-60 pointer-events-none"></div>

        <!-- Logo -->
        <a href="/" wire:navigate class="flex items-center gap-2 relative z-10 transition-transform hover:scale-105 duration-300">
            <svg class="w-8 h-8 text-sage-700"><use href="#brand-mark"/></svg>
            <span class="font-serif text-2xl text-ink-900 tracking-tight">Re<span class="text-sun-400">·</span>Life</span>
        </a>
        
        <!-- Pati İzi Sağ Üstte -->
        <svg class="absolute top-12 right-12 w-24 h-24 text-cream-200/60 rotate-[15deg] transition-transform hover:rotate-45 duration-700"><use href="#brand-mark"/></svg>

        <div class="max-w-md relative z-10 mt-12 animate-fade-in-up">
            <h1 class="font-serif text-[56px] leading-[1.05] text-ink-900 tracking-tight">
                Sevgiyle <span class="italic font-hand text-[64px] text-clay-600">başlayan</span>
                <br>küçük bir <span class="italic font-hand text-[64px] text-sage-600">kapı.</span>
            </h1>
            <p class="mt-8 text-[15px] text-ink-700/80 leading-relaxed max-w-sm">
                Hayvan sahibi olmak için onları evinize almanız gerekmiyor. Burada bir dostun hikâyesini takip etmenin ve hayatına dokunmanın en sıcak yolu var.
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
        <div class="relative mt-20 h-64 z-10 flex group">
            <!-- 1. Kart -->
            <div class="absolute left-0 top-4 w-44 h-48 bg-[#f6f2e8] rounded-lg shadow-sm border border-cream-200 p-2.5 transform -rotate-[5deg] transition-all duration-500 hover:-translate-y-4 hover:rotate-0 hover:shadow-xl hover:z-30 cursor-pointer">
                <div class="w-full h-32 bg-[#e6dfcc] rounded-[4px] overflow-hidden relative group-hover:bg-[#d8cfb8] transition-colors bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1543466835-00a7907e9de1?q=80&w=400&auto=format&fit=crop');"></div>
                <div class="mt-3 text-center font-hand text-sm text-ink-700 tracking-widest uppercase">poyraz</div>
            </div>
            
            <!-- 2. Kart (Ortadaki) -->
            <div class="absolute left-36 top-16 w-52 h-56 bg-[#fcf5de] rounded-lg shadow-md border border-[#f3e6be] p-4 transform rotate-[4deg] z-20 transition-all duration-500 hover:-translate-y-4 hover:rotate-0 hover:shadow-2xl hover:z-40 cursor-pointer">
                <div class="w-full h-20 bg-[#f4e6bb] rounded-[4px] flex items-center justify-center text-[10px] tracking-widest text-[#a88d3e] uppercase bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?q=80&w=400&auto=format&fit=crop');">
                    <span class="bg-black/30 text-white px-2 py-0.5 rounded backdrop-blur-sm">LEYLA</span>
                </div>
                <div class="mt-4 font-hand text-[15px] text-ink-800 leading-[1.3]">
                    "bugün ilk kez bana baktı
                    — bütün dünya durdu
                    sandım."
                </div>
                <div class="mt-2 text-[9px] text-[#a88d3e] uppercase tracking-widest">- CAMILLE</div>
            </div>

            <!-- 3. Kart -->
            <div class="absolute left-80 top-6 w-44 h-48 bg-[#e9f0e6] rounded-lg shadow-sm border border-[#d6e2d1] p-2.5 transform rotate-[5deg] transition-all duration-500 hover:-translate-y-4 hover:rotate-0 hover:shadow-xl hover:z-30 cursor-pointer">
                <div class="w-full h-32 bg-[#c9d8c3] rounded-[4px] flex items-center justify-center text-[10px] tracking-widest text-[#6c8663] uppercase transition-colors group-hover:bg-[#b8c9b1] bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1517849845537-4d257902454a?q=80&w=400&auto=format&fit=crop');">
                    <span class="bg-black/30 text-white px-2 py-0.5 rounded backdrop-blur-sm">MİLO</span>
                </div>
                <div class="mt-3 text-center font-hand text-sm text-ink-700 tracking-widest uppercase">milo</div>
            </div>
        </div>

        <div class="mt-auto flex items-end justify-between relative z-10">
            <!-- Sol alt yaprak -->
            <div class="w-8 h-12 bg-sage-300/40 rounded-t-full rounded-br-full rounded-bl-sm transform -rotate-12"></div>
            <div class="text-[10px] text-ink-900/40 tracking-widest uppercase mb-1">
                RE·LIFE · SICAK BİR KUCAK İÇİN · MMXXVI
            </div>
        </div>
    </div>

    <!-- SAĞ TARAF (Form) -->
    <div class="w-full lg:w-1/2 flex items-center justify-center bg-cream-100 px-6 sm:px-8 py-12 relative overflow-y-auto">
        <div class="w-full max-w-[420px] animate-fade-in-up">
            <div class="mb-8">
                <div class="text-[11px] font-semibold text-ink-900/40 tracking-[0.2em] uppercase mb-2">Hoş Geldin</div>
                <h2 class="font-serif text-[42px] leading-[1.1] text-ink-900">İçeri gel, biri seni bekliyor.</h2>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Role Selector (Sadece Görsel) -->
            <div class="grid grid-cols-3 gap-3 mb-8">
                <!-- 1. İyileştirici Dost -->
                <div class="bg-white border border-cream-200 hover:border-cream-300 hover:shadow-md rounded-[20px] p-3.5 text-left transition-all duration-500 flex flex-col relative overflow-hidden group hover:scale-105 cursor-default">
                    
                    <!-- Normal Görünüm -->
                    <div class="relative z-10 transition-all duration-500 transform group-hover:-translate-y-8 group-hover:opacity-0">
                        <svg class="w-4 h-4 mb-2.5 text-clay-400 group-hover:text-sage-500 transition-colors"><use href="#heart"/></svg>
                        <div class="text-[12px] font-semibold text-ink-900 mb-1">İyileştirici Dost</div>
                        <div class="text-[10px] text-ink-700/60 leading-tight pr-2">Bağışla, takip et hikayeleri oku</div>
                    </div>

                    <!-- Hover Durumu (İlham Verici Mesaj) -->
                    <div class="absolute inset-0 bg-sage-700 p-3.5 flex flex-col items-center justify-center text-center opacity-0 group-hover:opacity-100 transition-all duration-500 z-20 translate-y-8 group-hover:translate-y-0">
                        <span class="text-[9px] font-bold text-sun-300 tracking-[0.15em] uppercase mb-1.5">İyileştirici Dost</span>
                        <span class="text-[11px] font-medium text-cream-50 leading-snug">Bir hayata dokun, minik bir destekle onların kahramanı ol. 🐾</span>
                    </div>
                </div>
                
                <!-- 2. Gönüllü -->
                <div class="bg-white border border-cream-200 hover:border-cream-300 hover:shadow-md rounded-[20px] p-3.5 text-left transition-all duration-500 flex flex-col relative overflow-hidden group hover:scale-105 cursor-default">
                    
                    <!-- Normal Görünüm -->
                    <div class="relative z-10 transition-all duration-500 transform group-hover:-translate-y-8 group-hover:opacity-0">
                        <svg class="w-4 h-4 mb-2.5 text-clay-400 group-hover:text-sage-500 transition-colors"><use href="#brand-mark"/></svg>
                        <div class="text-[12px] font-semibold text-ink-900 mb-1">Gönüllü</div>
                        <div class="text-[10px] text-ink-700/60 leading-tight pr-2">Bakım notu düş, fotoğraf paylaş</div>
                    </div>

                    <!-- Hover Durumu (İlham Verici Mesaj) -->
                    <div class="absolute inset-0 bg-clay-600 p-3.5 flex flex-col items-center justify-center text-center opacity-0 group-hover:opacity-100 transition-all duration-500 z-20 translate-y-8 group-hover:translate-y-0">
                        <span class="text-[9px] font-bold text-sun-300 tracking-[0.15em] uppercase mb-1.5">Gönüllü</span>
                        <span class="text-[11px] font-medium text-cream-50 leading-snug">Sevginle iyileştir, zamanınla barınakları sıcacık bir yuvaya çevir. ❤️</span>
                    </div>
                </div>

                <!-- 3. Barınak -->
                <div class="bg-white border border-cream-200 hover:border-cream-300 hover:shadow-md rounded-[20px] p-3.5 text-left transition-all duration-500 flex flex-col relative overflow-hidden group hover:scale-105 cursor-default">
                    
                    <!-- Normal Görünüm -->
                    <div class="relative z-10 transition-all duration-500 transform group-hover:-translate-y-8 group-hover:opacity-0">
                        <svg class="w-4 h-4 mb-2.5 text-clay-400 group-hover:text-sage-500 transition-colors"><use href="#leaf"/></svg>
                        <div class="text-[12px] font-semibold text-ink-900 mb-1">Barınak</div>
                        <div class="text-[10px] text-ink-700/60 leading-tight pr-2">Dostları kaydet, durumu güncelle</div>
                    </div>

                    <!-- Hover Durumu (İlham Verici Mesaj) -->
                    <div class="absolute inset-0 bg-[#a88d3e] p-3.5 flex flex-col items-center justify-center text-center opacity-0 group-hover:opacity-100 transition-all duration-500 z-20 translate-y-8 group-hover:translate-y-0">
                        <span class="text-[9px] font-bold text-cream-100 tracking-[0.15em] uppercase mb-1.5">Barınak</span>
                        <span class="text-[11px] font-medium text-cream-50 leading-snug">Onların güvenli limanısınız. Sisteme katılın, hep birlikte büyüyelim. 🏡</span>
                    </div>
                </div>
            </div>

            <form wire:submit="login">
                <!-- E-POSTA -->
                <div class="mb-5 relative">
                    <label for="email" class="block text-[11px] font-semibold text-ink-900/40 tracking-[0.1em] uppercase mb-2">E-Posta</label>
                    <div class="relative">
                        <input wire:model="form.email" id="email" type="email" required autofocus
                            placeholder="adin@ornek.com"
                            class="w-full bg-white border-cream-200 rounded-full px-5 py-3.5 pl-11 text-[14px] text-ink-900 placeholder:text-ink-900/20 focus:ring-sage-400 focus:border-sage-400 shadow-[0_2px_10px_rgba(0,0,0,0.02)] transition-shadow hover:shadow-[0_4px_15px_rgba(0,0,0,0.04)] {{ $errors->has('form.email') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : '' }}">
                        <svg class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 {{ $errors->has('form.email') ? 'text-red-400' : 'text-ink-900/30' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
                </div>

                <!-- PAROLA -->
                <div class="mb-5 relative">
                    <label for="password" class="block text-[11px] font-semibold text-ink-900/40 tracking-[0.1em] uppercase mb-2">Parola</label>
                    <div class="relative">
                        <input wire:model="form.password" id="password" type="password" required
                            placeholder="parolan"
                            class="w-full bg-white border-cream-200 rounded-full px-5 py-3.5 pl-11 text-[14px] text-ink-900 placeholder:text-ink-900/20 focus:ring-sage-400 focus:border-sage-400 shadow-[0_2px_10px_rgba(0,0,0,0.02)] transition-shadow hover:shadow-[0_4px_15px_rgba(0,0,0,0.04)] {{ $errors->has('form.password') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : '' }}">
                        <svg class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 {{ $errors->has('form.password') ? 'text-red-400' : 'text-ink-900/30' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between mb-8">
                    <label for="remember" class="flex items-center gap-2 cursor-pointer group">
                        <input wire:model="form.remember" id="remember" type="checkbox" class="rounded text-sage-700 border-cream-300 focus:ring-sage-500 shadow-sm w-4 h-4 transition-colors">
                        <span class="text-[13px] text-ink-700 group-hover:text-ink-900 transition-colors">Beni hatırla</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" wire:navigate class="text-[12px] text-ink-700 hover:text-ink-900 underline underline-offset-4 decoration-ink-900/20 hover:decoration-ink-900 transition-all">
                            Parolamı unuttum
                        </a>
                    @endif
                </div>

                <button type="submit" wire:loading.attr="disabled" wire:target="login" class="w-full bg-[#596d53] hover:bg-[#4c5f46] text-[#f7fae8] rounded-full py-4 text-[14px] font-medium transition-all duration-300 shadow-sm hover:shadow-md flex items-center justify-center gap-2 disabled:opacity-75 disabled:cursor-wait relative overflow-hidden group">
                    <span wire:loading.remove wire:target="login" class="flex items-center gap-2 transform transition-transform group-hover:translate-x-1">Giriş Yap <span>→</span></span>
                    <span wire:loading wire:target="login" class="flex items-center gap-2">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Giriş yapılıyor...
                    </span>
                </button>
            </form>

            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-cream-300/60"></div>
                </div>
                <div class="relative flex justify-center text-[10px] tracking-widest text-ink-900/30 uppercase">
                    <span class="bg-cream-100 px-3">YA DA</span>
                </div>
            </div>

            <div class="space-y-3">
                <button type="button" class="w-full bg-white hover:bg-cream-50 hover:border-cream-300 border border-cream-200 rounded-full py-3.5 text-[13px] font-medium text-ink-800 transition-all shadow-[0_1px_2px_rgba(0,0,0,0.02)] hover:shadow-sm flex items-center justify-center gap-3 group">
                    <svg class="w-4 h-4 text-[#4285F4] transition-transform group-hover:scale-110" viewBox="0 0 24 24"><path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                    Google ile devam et
                </button>
                <button type="button" class="w-full bg-white hover:bg-cream-50 hover:border-cream-300 border border-cream-200 rounded-full py-3.5 text-[13px] font-medium text-ink-800 transition-all shadow-[0_1px_2px_rgba(0,0,0,0.02)] hover:shadow-sm flex items-center justify-center gap-3 group">
                    <svg class="w-4 h-4 text-ink-900 transition-transform group-hover:scale-110" viewBox="0 0 24 24"><path fill="currentColor" d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.04 2.34-.85 3.73-.78 1.07.05 2.16.51 2.87 1.34-2.84 1.56-2.28 5.61.43 6.66-1.01 2.4-2.12 4.01-2.11 4.95zm-3.5-14.3c-.63 2.17-2.89 3.66-4.94 3.3.49-2.19 2.5-3.79 4.94-3.3z"/></svg>
                    Apple ile devam et
                </button>
            </div>

            <div class="mt-8 text-center text-[12px] text-ink-700">
                Re·Life ailesinde değil misin? 
                <a href="{{ route('register') }}" wire:navigate class="font-semibold text-clay-600 hover:text-clay-700 underline underline-offset-4 decoration-clay-300 transition-colors">Hemen katıl</a>
            </div>
        </div>
    </div>
</div>
