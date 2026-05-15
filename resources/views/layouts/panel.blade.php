<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $title ?? 'Re·Life — Panel' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="paper min-h-screen font-sans text-ink-800 antialiased">

@include('partials.svg-defs')

@php
    $isSuper = auth()->user()?->isSuperadmin();
    $nav = $isSuper ? [
        ['superadmin.dashboard', 'Genel Bakış'],
        ['superadmin.approvals', 'Barınak Onayları'],
        ['superadmin.shelters', 'Tüm Barınaklar'],
        ['superadmin.users', 'Kullanıcılar'],
        ['superadmin.badges', 'Rozetler'],
    ] : [
        ['admin.dashboard', 'Genel Bakış'],
        ['admin.animals', 'Hayvanlar'],
        ['admin.needs', 'İhtiyaçlar'],
        ['admin.donations', 'Bağışlar'],
        ['admin.donors', 'Bağışçılar'],
        ['admin.announcements', 'Duyurular'],
        ['admin.shelter', 'Barınak Profili'],
    ];
@endphp

<div class="max-w-[1320px] mx-auto px-5 sm:px-8 py-6 flex gap-7">
    <aside class="w-60 shrink-0 hidden lg:block">
        <a class="flex items-center gap-3 mb-7" href="{{ route('home') }}">
            <svg class="w-9 h-9"><use href="#brand-mark"/></svg>
            <span class="font-serif text-xl text-ink-900">Re·Life</span>
        </a>
        <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500 mb-3 px-3">
            {{ $isSuper ? 'Platform Yönetimi' : 'Barınak Paneli' }}
        </div>
        <nav class="space-y-1">
            @foreach($nav as [$routeName, $label])
                <a href="{{ route($routeName) }}"
                   class="block rounded-2xl px-4 py-2.5 text-[14px] transition
                   {{ request()->routeIs($routeName) ? 'bg-sage-600 text-cream-50 shadow-card' : 'text-ink-700 hover:bg-cream-100' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
        <div class="mt-6 pt-5 border-t border-cream-300/60 px-3 space-y-2">
            <a href="{{ route('home') }}" class="block text-[13px] text-ink-700/70 hover:text-ink-900">← Siteye dön</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-[13px] text-ink-700/55 hover:text-ink-900">Çıkış yap</button>
            </form>
        </div>
    </aside>

    <main class="flex-1 min-w-0">
        {{ $slot }}
    </main>
</div>

@livewireScripts
</body>
</html>
