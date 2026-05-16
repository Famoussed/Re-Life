<div>
    <header class="flex items-center justify-between gap-4 mb-7">
        <div>
            <h1 class="font-modern text-[34px] leading-tight text-ink-900">Duyurular</h1>
            <p class="text-[14px] text-ink-700/65 mt-1">Bağışçılarına haberlerini ulaştır.</p>
        </div>
        @unless($showForm)
            <button wire:click="create"
                class="rounded-full bg-sage-600 hover:bg-sage-700 text-cream-50 px-5 py-2.5 text-[14px] font-medium">
                + Yeni Duyuru
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
            <h2 class="font-modern text-[22px] text-ink-900 mb-4">Yeni Duyuru</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-[13px] text-ink-700 mb-1">Başlık</label>
                    <input type="text" wire:model="title"
                        class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400">
                    @error('title') <span class="text-[12px] text-clay-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[13px] text-ink-700 mb-1">İçerik</label>
                    <textarea wire:model="body" rows="5"
                        class="w-full rounded-2xl border-cream-300/60 bg-cream-50 focus:ring-sage-400 focus:border-sage-400"></textarea>
                    @error('body') <span class="text-[12px] text-clay-500">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="flex items-center gap-3 mt-5">
                <button type="submit"
                    class="rounded-full bg-sage-600 hover:bg-sage-700 text-cream-50 px-5 py-2.5 text-[14px] font-medium">
                    Yayınla
                </button>
                <button type="button" wire:click="cancel"
                    class="rounded-full bg-cream-100 hover:bg-cream-200 text-ink-700 px-5 py-2.5 text-[14px]">
                    Vazgeç
                </button>
            </div>
        </form>
    @endif

    {{-- LİSTE --}}
    @if($announcements->isEmpty())
        <div class="paper-card rounded-4xl border border-cream-300/50 p-12 text-center">
            <div class="font-modern text-[24px] text-clay-500">henüz duyuru yayınlanmamış</div>
        </div>
    @else
        <div class="space-y-4">
            @foreach($announcements as $announcement)
                <article class="paper-note rounded-3xl p-5 shadow-note" wire:key="announcement-{{ $announcement->id }}">
                    <div class="flex items-start justify-between gap-4">
                        <h3 class="font-modern text-[19px] text-ink-900">{{ $announcement->title }}</h3>
                        <span class="text-[12px] text-ink-700/55 shrink-0">{{ $announcement->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    <p class="text-[14px] text-ink-700/80 mt-2 leading-[1.6] whitespace-pre-line">{{ $announcement->body }}</p>
                </article>
            @endforeach
        </div>
    @endif
</div>
