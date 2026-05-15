<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $title ?? 'Re·Life — Dostluk Albümü' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="paper min-h-screen font-sans text-ink-800 antialiased">

@include('partials.svg-defs')

<header class="relative z-20">
    <div class="max-w-[1320px] mx-auto px-5 sm:px-8 pt-6 pb-4 flex items-center gap-6 flex-wrap">
        <a class="flex items-center gap-3" href="{{ route('home') }}">
            <svg class="w-10 h-10"><use href="#brand-mark"/></svg>
            <span class="font-serif text-2xl text-ink-900 tracking-tight">Re<span class="text-sun-400">·</span>Life</span>
        </a>
        <nav class="hidden md:flex items-center gap-6 text-[14.5px] text-ink-700/80 ml-2">
            <a class="hover:text-ink-900" href="{{ route('home') }}">Dostlarımız</a>
            <a class="hover:text-ink-900" href="{{ route('leaderboard') }}">Sıralama</a>
        </nav>
        <div class="flex-1"></div>

        @auth
            @php($u = auth()->user())
            @if($u->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="text-[14px] text-sage-700 hover:text-sage-600">Barınak Paneli</a>
            @elseif($u->isSuperadmin())
                <a href="{{ route('superadmin.dashboard') }}" class="text-[14px] text-sage-700 hover:text-sage-600">Yönetim</a>
            @endif
            <a href="{{ route('notifications') }}" class="relative w-9 h-9 rounded-full paper-card border border-cream-300/60 flex items-center justify-center shadow-note" title="Bildirimler">
                <svg class="w-4 h-4 text-clay-500"><use href="#heart" fill="currentColor"/></svg>
                @php($unread = $u->unreadNotifications()->count())
                @if($unread > 0)
                    <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-clay-500 text-cream-50 text-[10px] font-semibold flex items-center justify-center">{{ $unread }}</span>
                @endif
            </a>
            <a href="{{ route('users.show', $u) }}" class="text-[14px] text-ink-700 hover:text-ink-900 font-medium">{{ $u->name }}</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-[13px] text-ink-700/55 hover:text-ink-900">Çıkış</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="text-[14px] text-ink-700/80 hover:text-ink-900">Giriş</a>
            <a href="{{ route('register') }}" class="rounded-full bg-sage-600 hover:bg-sage-700 text-cream-50 px-5 py-2.5 text-[14px] font-medium shadow-card">Aramıza Katıl</a>
        @endauth
    </div>
</header>

<main>
    {{ $slot }}
</main>

<footer class="border-t border-cream-300/50 mt-10">
    <div class="max-w-[1320px] mx-auto px-5 sm:px-8 py-10 grid sm:grid-cols-12 gap-8 items-start">
        <div class="sm:col-span-6">
            <div class="flex items-center gap-3">
                <svg class="w-9 h-9"><use href="#brand-mark"/></svg>
                <span class="font-serif text-2xl text-ink-900">Re·Life</span>
            </div>
            <p class="mt-3 text-[14px] text-ink-700/70 max-w-[420px] leading-[1.55]">
                Bir iyileştirici dostuz — operatör değil. Her dostun yolculuğunu birlikte yürürüz;
                sayılar arkada kalır, isimler ön sırada.
            </p>
            <div class="font-hand text-[22px] text-clay-500 mt-3">— sevgiyle</div>
        </div>
        <div class="sm:col-span-3">
            <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500 mb-3">Dostlar</div>
            <ul class="space-y-2 text-[13.5px] text-ink-700/85">
                <li><a href="{{ route('home') }}" class="hover:text-ink-900">Tüm albüm</a></li>
                <li><a href="{{ route('leaderboard') }}" class="hover:text-ink-900">Sıralama</a></li>
            </ul>
        </div>
        <div class="sm:col-span-3">
            <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500 mb-3">Barınaklar</div>
            <ul class="space-y-2 text-[13.5px] text-ink-700/85">
                <li><a href="{{ route('admin.register') }}" class="hover:text-ink-900">Barınağını kaydet</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-cream-300/50">
        <div class="max-w-[1320px] mx-auto px-5 sm:px-8 py-4 flex items-center justify-between text-[12px] text-ink-700/55">
            <span>© {{ date('Y') }} Re·Life — sıcak bir kucak için.</span>
            <span class="font-hand text-[18px] text-clay-500">tüm dostlara teşekkürler 🌿</span>
        </div>
    </div>
</footer>

@livewireScripts
</body>
</html>
