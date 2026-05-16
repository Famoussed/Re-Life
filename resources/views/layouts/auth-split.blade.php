<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $title ?? 'Giriş Yap — Re·Life' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans text-ink-800 antialiased min-h-screen flex bg-cream-50">

@include('partials.svg-defs')

{{ $slot }}

@livewireScripts
</body>
</html>
