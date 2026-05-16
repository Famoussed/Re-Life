<div>
    {{-- HERO --}}
    <section class="max-w-[1320px] mx-auto px-5 sm:px-8 pt-6 pb-12">
        <div class="grid lg:grid-cols-[1.15fr,0.85fr] gap-10 lg:gap-12 items-center">
            {{-- Sol: Metin --}}
            <div>
                <h1 class="font-serif text-[44px] sm:text-[68px] leading-[0.96] text-ink-900 tracking-tight max-w-[820px]">
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
            </div>

            {{-- Sağ: Dekoratif polaroid kolajı --}}
            <div class="relative hidden lg:block h-[360px]" aria-hidden="true">
                {{-- Yumuşak renk lekeleri --}}
                <span class="absolute top-4 right-2 w-56 h-56 rounded-full bg-sun-200/45 blur-3xl"></span>
                <span class="absolute bottom-0 left-4 w-52 h-52 rounded-full bg-sage-200/45 blur-3xl"></span>

                {{-- Arka polaroid --}}
                <div class="absolute top-2 right-6 w-[210px] tilt-2 paper-card rounded-2xl border border-cream-300/60 shadow-card p-2.5">
                    <div class="photo sage h-[170px] rounded-xl bg-cover bg-center"
                         style="background-image:url('https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?q=80&w=600&auto=format&fit=crop')"></div>
                    <div class="font-hand text-[19px] text-clay-500 text-center pt-1.5">yeni bir başlangıç</div>
                </div>

                {{-- Ön polaroid --}}
                <div class="absolute bottom-4 left-2 w-[230px] note-tilt-1 paper-card rounded-2xl border border-cream-300/60 shadow-lift p-2.5">
                    <div class="photo clay h-[185px] rounded-xl bg-cover bg-center"
                         style="background-image:url('https://images.unsplash.com/photo-1552053831-71594a27632d?q=80&w=600&auto=format&fit=crop')"></div>
                    <div class="font-hand text-[19px] text-sage-600 text-center pt-1.5">birlikte iyiyiz</div>
                </div>

                {{-- Yapışkan not — istatistik --}}
                <div class="absolute top-0 left-6 note-tilt-2 paper-note rounded-2xl shadow-note px-4 py-3 flex items-center gap-2.5">
                    <svg class="w-7 h-7 text-clay-500"><use href="#heart" fill="currentColor"/></svg>
                    <div class="leading-tight">
                        <div class="font-serif text-[22px] text-ink-900">{{ $totalActive }}</div>
                        <div class="text-[11px] text-ink-700/65 uppercase tracking-wide">dost umutla bekliyor</div>
                    </div>
                </div>

                {{-- Küçük paw süsü --}}
                <svg class="absolute bottom-2 right-10 w-9 h-9 text-sage-300 rotate-12"><use href="#paw" fill="currentColor"/></svg>
            </div>
        </div>
    </section>

    {{-- FİLTRE --}}
    <section class="max-w-[1320px] mx-auto px-5 sm:px-8" id="barinaklar">
        <div class="paper-card rounded-3xl border border-cream-300/60 p-4 flex flex-wrap items-center gap-3">
            <span class="font-serif text-[20px] text-ink-900 mr-2">Bugün kimi tanımak istersin?</span>

            <div class="flex flex-wrap gap-2 items-center">
                <button wire:click="$set('species', '')"
                    class="rounded-full px-4 py-1.5 text-[13px] border {{ $species === '' ? 'bg-sage-600 text-cream-50 border-sage-600' : 'bg-cream-100 text-ink-700 border-cream-300/60 hover:bg-cream-200' }}">
                    Hepsi
                </button>

                <!-- Kedi Grubu -->
                <div x-data="{ open: false }" class="relative" @click.away="open = false">
                    <button @click="open = !open" 
                        class="rounded-full px-4 py-1.5 text-[13px] border flex items-center gap-1 {{ in_array($species, ['cat', 'kitten']) ? 'bg-sage-600 text-cream-50 border-sage-600' : 'bg-cream-100 text-ink-700 border-cream-300/60 hover:bg-cream-200' }}">
                        Kedi
                        <svg class="w-3 h-3 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-transition.opacity
                        class="absolute top-full left-0 mt-1 w-36 bg-cream-50 border border-cream-300/60 rounded-xl shadow-sm overflow-hidden z-10 flex flex-col py-1" style="display: none;">
                        <button wire:click="$set('species', 'cat')" @click="open = false"
                            class="px-4 py-2 text-[13px] text-left hover:bg-cream-100 {{ $species === 'cat' ? 'font-medium text-sage-600' : 'text-ink-700' }}">
                            Yetişkin Kedi
                        </button>
                        <button wire:click="$set('species', 'kitten')" @click="open = false"
                            class="px-4 py-2 text-[13px] text-left hover:bg-cream-100 {{ $species === 'kitten' ? 'font-medium text-sage-600' : 'text-ink-700' }}">
                            Yavru Kedi
                        </button>
                    </div>
                </div>

                <!-- Köpek Grubu -->
                <div x-data="{ open: false }" class="relative" @click.away="open = false">
                    <button @click="open = !open" 
                        class="rounded-full px-4 py-1.5 text-[13px] border flex items-center gap-1 {{ in_array($species, ['dog', 'puppy']) ? 'bg-sage-600 text-cream-50 border-sage-600' : 'bg-cream-100 text-ink-700 border-cream-300/60 hover:bg-cream-200' }}">
                        Köpek
                        <svg class="w-3 h-3 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-transition.opacity
                        class="absolute top-full left-0 mt-1 w-36 bg-cream-50 border border-cream-300/60 rounded-xl shadow-sm overflow-hidden z-10 flex flex-col py-1" style="display: none;">
                        <button wire:click="$set('species', 'dog')" @click="open = false"
                            class="px-4 py-2 text-[13px] text-left hover:bg-cream-100 {{ $species === 'dog' ? 'font-medium text-sage-600' : 'text-ink-700' }}">
                            Yetişkin Köpek
                        </button>
                        <button wire:click="$set('species', 'puppy')" @click="open = false"
                            class="px-4 py-2 text-[13px] text-left hover:bg-cream-100 {{ $species === 'puppy' ? 'font-medium text-sage-600' : 'text-ink-700' }}">
                            Yavru Köpek
                        </button>
                    </div>
                </div>
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
                @foreach(\App\Enums\Animal\NeedType::cases() as $t)
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
