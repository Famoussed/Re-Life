<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $title ?? 'Re·Life' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="paper min-h-screen font-sans text-ink-800 antialiased">

@include('partials.svg-defs')

<div class="min-h-screen flex flex-col items-center justify-center px-5 py-12">
    <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3 mb-6">
        <svg class="w-12 h-12"><use href="#brand-mark"/></svg>
        <span class="font-modern text-3xl text-ink-900 tracking-tight">Re<span class="text-sun-400">·</span>Life</span>
    </a>

    <div class="w-full sm:max-w-md paper-card rounded-4xl shadow-card border border-cream-300/50 px-7 py-8">
        {{ $slot }}
    </div>

    <p class="mt-6 font-modern text-[20px] text-clay-500">her dostun bir hikâyesi var 🌿</p>
</div>

@livewireScripts
</body>
</html>
