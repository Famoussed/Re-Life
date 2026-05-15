<div>
    {{-- HERO --}}
    <section class="max-w-[1320px] mx-auto px-5 sm:px-8 pt-6 pb-12">
        <div class="inline-flex items-center gap-2 paper-card rounded-full px-3.5 py-1.5 border border-clay-200/60 text-[12px] text-clay-600 tracking-wide uppercase">
            <span class="w-1.5 h-1.5 rounded-full bg-clay-400 pulse-soft"></span>
            Dostluk Albümü
        </div>
        <h1 class="mt-5 font-serif text-[44px] sm:text-[68px] leading-[0.96] text-ink-900 tracking-tight max-w-[820px]">
            Her dostun bir <em class="italic text-clay-500 doodle">hikâyesi</em>,
            her hikâyenin bir <em class="italic text-sage-600">eli</em> var.
        </h1>
        <p class="mt-5 text-[16px] sm:text-[17px] text-ink-700/85 max-w-[560px] leading-[1.6]">
            Burada onları sayılarla değil isimleriyle tanırsınız. Yolculuklarının bir adımına
            ortak olun — gerisini birlikte buluruz.
        </p>
        <div class="mt-7 flex items-center gap-5 text-[14px] text-ink-700/70 flex-wrap">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5 text-sage-600"><use href="#leaf" fill="currentColor"/></svg>
                {{ $totalActive }} dost iyileşme yolunda
            </span>
        </div>
    </section>

    {{-- FİLTRE --}}
    <section class="max-w-[1320px] mx-auto px-5 sm:px-8" id="barinaklar">
        <div class="paper-card rounded-3xl border border-cream-300/60 p-4 flex flex-wrap items-center gap-3">
            <span class="font-serif text-[20px] text-ink-900 mr-2">Bugün kimi tanımak istersin?</span>

            <div class="flex flex-wrap gap-2">
                <button wire:click="$set('species', '')"
                    class="rounded-full px-4 py-1.5 text-[13px] border {{ $species === '' ? 'bg-sage-600 text-cream-50 border-sage-600' : 'bg-cream-100 text-ink-700 border-cream-300/60 hover:bg-cream-200' }}">
                    Hepsi
                </button>
                @foreach(\App\Enums\AnimalSpecies::cases() as $s)
                    <button wire:click="$set('species', '{{ $s->value }}')"
                        class="rounded-full px-4 py-1.5 text-[13px] border {{ $species === $s->value ? 'bg-sage-600 text-cream-50 border-sage-600' : 'bg-cream-100 text-ink-700 border-cream-300/60 hover:bg-cream-200' }}">
                        {{ $s->label() }}
                    </button>
                @endforeach
            </div>

            <span class="w-px h-5 bg-ink-700/10 mx-1 self-center hidden sm:block"></span>

            <select wire:model.live="city"
                class="rounded-full bg-cream-100 border-cream-300/60 text-ink-700 text-[13px] py-1.5 pl-4 pr-9 focus:ring-sage-400 focus:border-sage-400">
                <option value="">Tüm şehirler</option>
                @foreach($cities as $c)
                    <option value="{{ $c }}">{{ $c }}</option>
                @endforeach
            </select>

            <select wire:model.live="needType"
                class="rounded-full bg-cream-100 border-cream-300/60 text-ink-700 text-[13px] py-1.5 pl-4 pr-9 focus:ring-sage-400 focus:border-sage-400">
                <option value="">Tüm ihtiyaçlar</option>
                @foreach(\App\Enums\NeedType::cases() as $t)
                    <option value="{{ $t->value }}">{{ $t->label() }}</option>
                @endforeach
            </select>

            @if($species || $city || $needType)
                <button wire:click="resetFilters" class="text-[12px] text-clay-500 hover:text-clay-600 underline underline-offset-2">filtreleri temizle</button>
            @endif

            <div class="flex-1"></div>
            <div class="text-[12px] text-ink-700/55 uppercase tracking-wide">{{ $animals->count() }} dost</div>
        </div>
    </section>

    {{-- GALERİ --}}
    <section class="max-w-[1320px] mx-auto px-5 sm:px-8 py-10">
        @if($animals->isEmpty())
            <div class="paper-card rounded-4xl border border-cream-300/50 p-12 text-center">
                <div class="font-hand text-[26px] text-clay-500">bu filtrelere uyan dost bulamadık</div>
                <p class="text-[14px] text-ink-700/60 mt-2">Filtreleri biraz gevşetmeyi dene.</p>
            </div>
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-7">
                @foreach($animals as $animal)
                    <x-animal-card :animal="$animal" wire:key="animal-{{ $animal->id }}" />
                @endforeach
            </div>
        @endif
    </section>
</div>
