<div class="max-w-[1320px] mx-auto px-5 sm:px-8 py-8" x-data="{ 
    activeTab: 'story', 
    showDonationModal: false,
    donationAmount: 220,
    isRecurring: false,
    presets: [
        { v: 75, label: 'bir günlük mama' },
        { v: 150, label: 'iki günlük bakım' },
        { v: 220, label: 'aşı + tasma' },
        { v: 500, label: 'bir hafta bakım' }
    ]
}">
    @php
        $tones = ['sage', 'clay', 'sun', 'peach', 'cream'];
        $tone = $tones[$animal->id % count($tones)];
        $activeNeeds = $animal->needs->where('status', \App\Enums\Animal\NeedStatus::Active);
        $completedNeeds = $animal->needs->where('status', \App\Enums\Animal\NeedStatus::Completed);
    @endphp

    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('home') }}" class="flex items-center gap-2 text-[14px] text-ink-700/70 hover:text-ink-900">
            <svg class="w-4 h-4 rotate-180" aria-hidden="true"><use href="#arrow" stroke="currentColor" fill="none"/></svg> Albüme dön
        </a>
        <div class="text-[12px] text-ink-700/55 uppercase tracking-[0.16em]">Hikâye · {{ $animal->name }}</div>
    </div>

    <div class="paper-card rounded-4xl shadow-card border border-cream-300/50 overflow-hidden relative">
        <div class="absolute -top-2 right-10 paper-note rounded-md px-3 py-1 shadow-note note-tilt-1 font-hand text-clay-600 text-[20px] z-10">{{ now()->format('d M y') }}</div>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
            <div class="lg:col-span-5 relative">
                <div class="photo {{ $tone }} photo-vignette lg:h-[460px] h-[300px] w-full">
                    @if($animal->photo_path)
                        <img src="{{ Storage::url($animal->photo_path) }}" alt="{{ $animal->name }}" class="w-full h-full object-cover mix-blend-multiply opacity-80" />
                    @endif
                    <span class="placeholder-mono absolute">{{ mb_strtolower($animal->name) }} · sıcak portre</span>
                </div>
                <div class="absolute bottom-4 left-4 paper-card rounded-full px-3.5 py-2 shadow-note flex items-center gap-2 border border-cream-300/60">
                    <span class="text-[18px]">🌞</span>
                    <span class="font-hand text-[19px] text-clay-600 leading-none">Bugün çok enerjik!</span>
                </div>
                <button class="absolute top-4 right-4 rounded-full px-3 py-2 shadow-note flex items-center gap-2 transition-colors bg-clay-500 text-cream-50 border border-cream-300/60">
                    <svg class="w-4 h-4" aria-hidden="true"><use href="#heart" fill="currentColor"/></svg>
                    <span class="text-[12px] font-medium">Dostum</span>
                </button>
                <div class="absolute top-4 left-4">
                    <span class="rounded-full bg-cream-100 text-clay-500 px-3 py-1 text-[11px] uppercase tracking-wider font-medium border border-cream-300/60">iyileşiyor</span>
                </div>
            </div>

            <div class="lg:col-span-7 p-8 flex flex-col">
                <div class="text-[11px] uppercase tracking-[0.16em] text-sage-700 font-medium">{{ $animal->species->label() }} · {{ $animal->gender->label() }} · {{ $animal->age_estimate }}</div>
                <h1 class="font-serif text-[60px] lg:text-[80px] leading-[0.92] text-ink-900 mt-1">{{ $animal->name }}</h1>
                <div class="font-hand text-[28px] text-clay-500 mt-1 leading-none">rüzgar gibi koşar, hafifçe durur</div>

                <div class="flex flex-wrap gap-1.5 mt-5">
                    <span class="rounded-full bg-sage-100 text-sage-700 border-sage-200 px-3 py-1 text-[12.5px] border whitespace-nowrap">Sakin</span>
                    <span class="rounded-full bg-clay-50 text-clay-600 border-clay-200 px-3 py-1 text-[12.5px] border whitespace-nowrap">Biraz Utangaç</span>
                    <span class="rounded-full bg-sun-100 text-clay-600 border-sun-200 px-3 py-1 text-[12.5px] border whitespace-nowrap">Yumuşak Kalpli</span>
                    <span class="rounded-full bg-peach-100 text-clay-600 border-peach-200 px-3 py-1 text-[12.5px] border whitespace-nowrap">Çocuk Dostu</span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mt-7 pt-6 border-t border-dashed border-clay-200">
                    <div class="lg:col-span-3">
                        <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500 mb-1.5">İyileşme yolculuğu</div>
                        <div class="font-serif italic text-[22px] text-ink-900 leading-tight">Yeniden koşmaya hazırlanıyor</div>
                        <div class="mt-3">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @for($i = 0; $i < 8; $i++)
                                    <svg width="26" height="26" class="{{ $i < 6 ? 'text-clay-500' : 'text-clay-200' }}"><use href="#paw" fill="currentColor"/></svg>
                                @endfor
                            </div>
                        </div>
                        <div class="flex justify-between mt-2 text-[12px] text-ink-700/65">
                            <span>6 adım atıldı</span>
                            <span class="text-clay-500 font-medium">2 adım kaldı</span>
                        </div>
                    </div>
                    <div class="lg:col-span-2 hidden lg:block">
                        <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500 mb-1.5">Bu hafta yanında</div>
                        <div class="font-serif text-[44px] text-ink-900 leading-none">184</div>
                        <div class="text-[12px] text-ink-700/65 mt-1">iyileştirici dost</div>
                        <div class="flex -space-x-1.5 mt-3">
                            <div class="w-7 h-7 rounded-full border-2 border-cream-50 bg-[#A8B891]"></div>
                            <div class="w-7 h-7 rounded-full border-2 border-cream-50 bg-[#E5C39E]"></div>
                            <div class="w-7 h-7 rounded-full border-2 border-cream-50 bg-[#F8C8A8]"></div>
                            <div class="w-7 h-7 rounded-full border-2 border-cream-50 bg-[#F2C246]"></div>
                            <div class="w-7 h-7 rounded-full border-2 border-cream-50 bg-[#C8D2B5]"></div>
                            <div class="w-7 h-7 rounded-full border-2 border-cream-50 bg-cream-200 text-[10px] font-semibold text-clay-600 flex items-center justify-center">+179</div>
                        </div>
                    </div>
                </div>

                <div class="flex-1"></div>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mt-6">
                    <button type="button" @click="showDonationModal = true" class="lg:col-span-2 rounded-full font-medium flex items-center justify-center gap-2 transition-colors px-6 py-3 text-[15px] bg-ink-900 hover:bg-ink-800 text-cream-50">
                        <svg class="w-4 h-4" aria-hidden="true"><use href="#paw" fill="currentColor"/></svg> Ona Bir Adım Hediye Et
                    </button>
                    <button class="rounded-full font-medium flex items-center justify-center gap-2 transition-colors px-6 py-3 text-[15px] bg-cream-100 hover:bg-cream-200 text-ink-700 border border-cream-300/60">
                        <svg class="w-4 h-4" aria-hidden="true"><use href="#check" stroke="currentColor" fill="none"/></svg> Listeme Ekle
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 flex items-center gap-2 border-b border-cream-300/60 overflow-x-auto whitespace-nowrap scrollbar-hide">
        <button @click="activeTab = 'story'" :class="{ 'border-clay-500 text-ink-900 font-semibold': activeTab === 'story', 'border-transparent text-ink-700/60 hover:text-ink-900': activeTab !== 'story' }" class="flex items-center gap-2 px-4 py-3 text-[14px] border-b-2 transition-colors">
            <svg class="w-4 h-4" aria-hidden="true"><use href="#heart" fill="currentColor"/></svg> Hikâye & Notlar
        </button>
        <button @click="activeTab = 'health'" :class="{ 'border-clay-500 text-ink-900 font-semibold': activeTab === 'health', 'border-transparent text-ink-700/60 hover:text-ink-900': activeTab !== 'health' }" class="flex items-center gap-2 px-4 py-3 text-[14px] border-b-2 transition-colors">
            <svg class="w-4 h-4" aria-hidden="true"><use href="#syringe" fill="currentColor"/></svg> Sağlık Günlüğü
        </button>
        <button @click="activeTab = 'needs'" :class="{ 'border-clay-500 text-ink-900 font-semibold': activeTab === 'needs', 'border-transparent text-ink-700/60 hover:text-ink-900': activeTab !== 'needs' }" class="flex items-center gap-2 px-4 py-3 text-[14px] border-b-2 transition-colors">
            <svg class="w-4 h-4" aria-hidden="true"><use href="#bone" fill="currentColor"/></svg> İhtiyaçları
        </button>
        <button @click="activeTab = 'photos'" :class="{ 'border-clay-500 text-ink-900 font-semibold': activeTab === 'photos', 'border-transparent text-ink-700/60 hover:text-ink-900': activeTab !== 'photos' }" class="flex items-center gap-2 px-4 py-3 text-[14px] border-b-2 transition-colors">
            <svg class="w-4 h-4" aria-hidden="true"><use href="#sun" fill="currentColor"/></svg> Albümü
        </button>
    </div>

    <div class="mt-8 relative min-h-[400px]">
        <!-- TABS -->
        <div x-show="activeTab === 'story'" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <div class="lg:col-span-7 paper-card rounded-4xl border border-cream-300/50 p-7 shadow-card">
                    <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500 font-medium">Yolculuğu</div>
                    <h3 class="font-serif text-[28px] text-ink-900 leading-tight mt-1">Şubat'tan bugüne</h3>
                    <div class="relative pl-9 mt-6">
                        <div class="absolute left-3 top-3 bottom-3 w-px bg-cream-300"></div>
                        
                        <div class="relative pb-7">
                            <div class="absolute -left-7 top-1 w-7 h-7 rounded-full flex items-center justify-center bg-cream-50 border border-cream-300/60">
                                <svg class="w-3.5 h-3.5 text-sage-600" aria-hidden="true"><use href="#leaf" fill="currentColor"/></svg>
                            </div>
                            <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500 mb-1 flex items-center gap-2">Şubat</div>
                            <div class="font-serif italic text-[17px] text-ink-900 leading-snug">Yola düştü, biz bulduk</div>
                            <div class="text-[13px] text-ink-700/65 mt-1 leading-snug">{{ $animal->shelter->name }}'a getirildi. Tanışma günü.</div>
                        </div>
                        
                        <div class="relative pb-7">
                            <div class="absolute -left-7 top-1 w-7 h-7 rounded-full flex items-center justify-center bg-cream-50 border border-cream-300/60">
                                <svg class="w-3.5 h-3.5 text-clay-500" aria-hidden="true"><use href="#heart" fill="currentColor"/></svg>
                            </div>
                            <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500 mb-1 flex items-center gap-2">Mart</div>
                            <div class="font-serif italic text-[17px] text-ink-900 leading-snug">İlk insan dokunuşu</div>
                            <div class="text-[13px] text-ink-700/65 mt-1 leading-snug">Gönüllülerin elinden ilk kez mama yedi.</div>
                        </div>

                        <div class="relative pb-7">
                            <div class="absolute -left-7 top-1 w-7 h-7 rounded-full flex items-center justify-center bg-cream-50 border border-cream-300/60">
                                <svg class="w-3.5 h-3.5 text-sun-400" aria-hidden="true"><use href="#sun" fill="currentColor"/></svg>
                            </div>
                            <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500 mb-1 flex items-center gap-2">Nisan</div>
                            <div class="font-serif italic text-[17px] text-ink-900 leading-snug">İlk parkta koşuşu</div>
                            <div class="text-[13px] text-ink-700/65 mt-1 leading-snug">Otuz dakika, hiç durmadı.</div>
                        </div>

                        <div class="relative">
                            <div class="absolute -left-7 top-1 w-7 h-7 rounded-full flex items-center justify-center bg-cream-50 border-2 border-clay-500">
                                <svg class="w-3.5 h-3.5 text-peach-400" aria-hidden="true"><use href="#paw" fill="currentColor"/></svg>
                            </div>
                            <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500 mb-1 flex items-center gap-2">
                                Mayıs <span class="paper-note rounded-md px-2 py-0.5 text-[9.5px] uppercase tracking-wider text-clay-600">şu an</span>
                            </div>
                            <div class="font-serif italic text-[17px] text-ink-900 leading-snug">Bir kapıyı bekliyor</div>
                            <div class="text-[13px] text-ink-700/65 mt-1 leading-snug">Aile aranıyor — sabırlı bir ev.</div>
                        </div>
                    </div>
                    
                    <div class="mt-8 pt-6 border-t border-dashed border-clay-200">
                        <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500 mb-1.5">Barınak Hikâyesi</div>
                        <p class="font-serif text-[18px] text-ink-900/90 leading-[1.6]">{{ $animal->story }}</p>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="paper-card rounded-4xl border border-cream-300/50 p-7 shadow-card">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500">Dost notları</div>
                                <h3 class="font-serif text-[26px] text-ink-900 leading-tight">Bugünden satırlar</h3>
                            </div>
                            <div class="font-hand text-[19px] text-sage-700 -rotate-3">14 not</div>
                        </div>
                        <div class="space-y-3">
                            <div class="paper-note rounded-2xl p-4 shadow-note note-tilt-1">
                                <div class="font-hand text-[20px] text-ink-700 leading-[1.25]">"Bugün ilk kez elimden mama yedi. Minicik bir an, ama gözü ışıldı."</div>
                                <div class="flex items-center gap-2 mt-2 text-[11px] text-ink-700/65 font-sans">
                                    <div class="w-5 h-5 rounded-full bg-sage-300"></div>
                                    <span class="font-semibold text-ink-700">Camille</span>
                                    <span>· koruyucu aile</span>
                                    <span class="ml-auto uppercase tracking-wider">2 SAAT ÖNCE</span>
                                </div>
                            </div>

                            <div class="bg-sage-50 border border-sage-200 rounded-2xl p-4 shadow-note note-tilt-2">
                                <div class="font-hand text-[20px] text-ink-700 leading-[1.25]">"Diğer köpeklerle parkta tanıştırdık. Yan yana uyudukları an — saklamak istedim."</div>
                                <div class="flex items-center gap-2 mt-2 text-[11px] text-ink-700/65 font-sans">
                                    <div class="w-5 h-5 rounded-full bg-sage-300"></div>
                                    <span class="font-semibold text-ink-700">Theo R.</span>
                                    <span>· yürüyüş gönüllüsü</span>
                                    <span class="ml-auto uppercase tracking-wider">DÜN</span>
                                </div>
                            </div>

                            <div class="bg-sun-50 border border-sun-200 rounded-2xl p-4 shadow-note note-tilt-1">
                                <div class="font-hand text-[20px] text-ink-700 leading-[1.25]">"Kuyruğunu sallamayı yeniden öğreniyor. Yazmak istemezdim ama yazıyorum çünkü gerçek."</div>
                                <div class="flex items-center gap-2 mt-2 text-[11px] text-ink-700/65 font-sans">
                                    <div class="w-5 h-5 rounded-full bg-sage-300"></div>
                                    <span class="font-semibold text-ink-700">Dr. Lía M.</span>
                                    <span>· veteriner</span>
                                    <span class="ml-auto uppercase tracking-wider">3 GÜN ÖNCE</span>
                                </div>
                            </div>
                        </div>
                        <button class="w-full mt-5 rounded-full px-5 py-2.5 text-[14px] bg-cream-100 hover:bg-cream-200 text-ink-700 border border-cream-300/60 font-medium">14 not daha oku</button>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'health'" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <div class="lg:col-span-7 paper-card rounded-4xl border border-cream-300/50 p-7 shadow-card">
                    <div class="flex items-baseline justify-between mb-5">
                        <div>
                            <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500">Aşı takvimi</div>
                            <h3 class="font-serif text-[28px] text-ink-900 leading-tight">İğnenin minik korkusu, büyük korumamız.</h3>
                        </div>
                        <div class="rounded-full bg-sage-50 border border-sage-200 px-3 py-1 text-[11px] uppercase tracking-wider text-sage-700">1/3 tamam</div>
                    </div>
                    
                    <div class="space-y-2.5">
                        <div class="rounded-2xl p-4 flex items-center gap-4 border bg-sage-50 border-sage-200">
                            <div class="w-11 h-11 rounded-2xl flex items-center justify-center bg-sage-300 text-sage-700">
                                <svg class="w-6 h-6" aria-hidden="true"><use href="#syringe" fill="currentColor"/></svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-[15px] font-medium text-ink-900">Kuduz Aşısı</div>
                                <div class="text-[12px] text-ink-700/60">Yapıldı · 12 Mart 2026</div>
                            </div>
                            <div class="flex items-center gap-1.5 text-sage-700 text-[12px] font-medium">
                                <svg class="w-4 h-4" aria-hidden="true"><use href="#check" stroke="currentColor" fill="none"/></svg> Tamamlandı
                            </div>
                        </div>
                        
                        <div class="rounded-2xl p-4 flex items-center gap-4 border bg-sun-50 border-sun-200">
                            <div class="w-11 h-11 rounded-2xl flex items-center justify-center bg-sun-200 text-clay-600">
                                <svg class="w-6 h-6" aria-hidden="true"><use href="#syringe" fill="currentColor"/></svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-[15px] font-medium text-ink-900">Karma Aşısı - 1. Doz</div>
                                <div class="text-[12px] text-ink-700/60">Yaklaşan tarih · 20 Mayıs 2026</div>
                            </div>
                            <div class="rounded-full bg-cream-50 border border-cream-300/60 px-3 py-1 text-[11px] uppercase tracking-wider text-clay-500">yaklaşıyor</div>
                        </div>

                        <div class="rounded-2xl p-4 flex items-center gap-4 border bg-sun-50 border-sun-200">
                            <div class="w-11 h-11 rounded-2xl flex items-center justify-center bg-sun-200 text-clay-600">
                                <svg class="w-6 h-6" aria-hidden="true"><use href="#syringe" fill="currentColor"/></svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-[15px] font-medium text-ink-900">Lyme Aşısı</div>
                                <div class="text-[12px] text-ink-700/60">Yaklaşan tarih · 10 Haziran 2026</div>
                            </div>
                            <div class="rounded-full bg-cream-50 border border-cream-300/60 px-3 py-1 text-[11px] uppercase tracking-wider text-clay-500">bekliyor</div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 space-y-4">
                    <div class="paper-card rounded-4xl border border-cream-300/50 p-6 shadow-card">
                        <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500">Sağlık özeti</div>
                        <h3 class="font-serif text-[24px] text-ink-900 leading-tight mt-1">Genel durum: <em class="italic text-sage-700">iyi yolda</em></h3>
                        
                        <p class="text-[14px] text-ink-700/80 mt-4 leading-relaxed">{{ $animal->health_status }}</p>

                        <div class="grid grid-cols-2 gap-3 mt-4">
                            <div class="rounded-2xl bg-cream-50 border border-cream-300/60 p-3">
                                <div class="text-[10.5px] uppercase tracking-wider text-clay-500">Kilo</div>
                                <div class="font-serif text-[20px] text-ink-900 leading-none mt-1">24.5 kg</div>
                                <div class="text-[11px] text-ink-700/55 mt-0.5">+1.2 kg / ay</div>
                            </div>
                            <div class="rounded-2xl bg-cream-50 border border-cream-300/60 p-3">
                                <div class="text-[10.5px] uppercase tracking-wider text-clay-500">Vücut Isısı</div>
                                <div class="font-serif text-[20px] text-ink-900 leading-none mt-1">38.6°C</div>
                                <div class="text-[11px] text-ink-700/55 mt-0.5">normal</div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-4xl p-6 shadow-card relative overflow-hidden" style="background: linear-gradient(135deg, #F2C246 0%, #E88753 100%);">
                        <svg class="absolute -right-6 -top-6 w-32 h-32 text-cream-50/25" aria-hidden="true"><use href="#paw" fill="currentColor"/></svg>
                        <div class="relative">
                            <div class="text-[11px] uppercase tracking-[0.16em] text-cream-50/85">Veteriner notu</div>
                            <div class="font-hand text-[24px] text-cream-50 leading-tight mt-1">"Toparlama hızı çok iyi. İyi besleniyor, gözleri parlıyor."</div>
                            <div class="text-[12px] text-cream-50/80 mt-3 uppercase tracking-wider">— Dr. Lía Mendoza · 05 May</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'needs'" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                @forelse($activeNeeds as $need)
                    @php
                        $pct = min(100, round($need->progressPercent()));
                        $toneClass = [
                            'veterinary' => 'bg-peach-100 border-peach-200 text-clay-600',
                            'food' => 'bg-sun-50 border-sun-200 text-clay-600',
                            'care' => 'bg-sage-50 border-sage-200 text-sage-700',
                            'other' => 'bg-clay-50 border-clay-200 text-clay-600',
                        ][$need->type->value] ?? 'bg-clay-50 border-clay-200 text-clay-600';
                        $icon = [
                            'veterinary' => 'syringe',
                            'food' => 'bone',
                            'care' => 'blanket',
                            'other' => 'heart',
                        ][$need->type->value] ?? 'paw';
                    @endphp
                    <div class="paper-card rounded-4xl border border-cream-300/50 p-6 shadow-card relative overflow-hidden">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center {{ $toneClass }} border">
                                <svg class="w-7 h-7" aria-hidden="true"><use href="#{{ $icon }}" fill="currentColor"/></svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-baseline justify-between gap-3">
                                    <div>
                                        <div class="font-serif text-[24px] text-ink-900 leading-tight">{{ $need->title }}</div>
                                        <div class="text-[12.5px] text-ink-700/65 mt-0.5">
                                            ₺{{ number_format((float) $need->collected_amount, 0, ',', '.') }} 
                                            <span class="text-ink-700/45">/ ₺{{ number_format((float) $need->target_amount, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="h-2.5 rounded-full bg-cream-200 overflow-hidden mt-4">
                            <div class="h-full rounded-full" style="width: {{ $pct }}%; background: linear-gradient(90deg, #C58A1B, #E8A92B, #E88753);"></div>
                        </div>
                        <div class="flex items-center justify-between mt-3">
                            <div class="text-[11px] uppercase tracking-wider text-clay-500">%{{ $pct }} tamamlandı</div>
                            <button type="button" @click="showDonationModal = true" class="rounded-full font-medium flex items-center justify-center gap-2 transition-colors px-4 py-2 text-[13px] bg-clay-500 hover:bg-clay-600 text-cream-50">
                                Bu ihtiyaca destek <svg class="w-3.5 h-3.5" aria-hidden="true"><use href="#arrow" stroke="currentColor" fill="none"/></svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="lg:col-span-2 rounded-2xl bg-sage-50 border border-sage-200 p-6 text-center shadow-note">
                        <svg class="w-8 h-8 text-sage-400 mx-auto mb-2" aria-hidden="true"><use href="#heart" fill="currentColor"/></svg>
                        <div class="font-serif text-[20px] text-ink-900">Şu an aktif bir ihtiyacı yok</div>
                        <p class="text-[14px] text-sage-700 mt-1">Yine de barınağına genel destek olabilirsin.</p>
                        <a href="{{ route('donate', ['shelter' => $animal->shelter_id]) }}" class="mt-4 inline-flex rounded-full bg-sage-600 hover:bg-sage-700 text-cream-50 px-5 py-2.5 text-[14px] font-medium transition-colors">
                            Barınağa Destek Ol
                        </a>
                    </div>
                @endforelse

                @foreach($completedNeeds as $need)
                    <div class="paper-card rounded-4xl border border-cream-300/50 p-6 shadow-card relative overflow-hidden opacity-80">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center bg-sage-50 border border-sage-200 text-sage-700">
                                <svg class="w-7 h-7" aria-hidden="true"><use href="#check" stroke="currentColor" fill="none"/></svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-baseline justify-between gap-3">
                                    <div>
                                        <div class="font-serif text-[24px] text-ink-900 leading-tight line-through">{{ $need->title }}</div>
                                        <div class="text-[12.5px] text-ink-700/65 mt-0.5">Tamamlandı</div>
                                    </div>
                                    <span class="rounded-full bg-sage-100 text-sage-700 px-2.5 py-1 text-[10.5px] uppercase tracking-wider border border-sage-200 whitespace-nowrap">
                                        <svg class="w-3 h-3 inline mr-1" aria-hidden="true"><use href="#check" stroke="currentColor" fill="none"/></svg>karşılandı
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="h-2.5 rounded-full bg-cream-200 overflow-hidden mt-4">
                            <div class="h-full rounded-full" style="width: 100%; background: linear-gradient(90deg, #6F875C, #8BA174);"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div x-show="activeTab === 'photos'" x-cloak>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                    $photoTones = ['sage','sun','peach','cream','clay','sage','sun','peach'];
                @endphp
                @foreach($photoTones as $i => $t)
                    <div class="paper-card rounded-3xl p-2 shadow-card {{ $i % 2 ? 'rotate-1' : '-rotate-1' }}">
                        <div class="photo {{ $t }} photo-vignette rounded-2xl h-[200px] w-full relative">
                            @if($i == 0 && $animal->photo_path)
                                <img src="{{ Storage::url($animal->photo_path) }}" class="w-full h-full object-cover mix-blend-multiply opacity-80" />
                            @endif
                            <span class="placeholder-mono absolute">{{ mb_strtolower($animal->name) }} · gün {{ $i*8 + 4 }}</span>
                        </div>
                        <div class="font-hand text-[18px] text-clay-600 text-center mt-1.5 leading-none">{{ mb_strtolower($animal->name) }} · {{ $i+1 }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- DONATION MODAL -->
    <div x-show="showDonationModal" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
        <div class="absolute inset-0 bg-ink-900/55 backdrop-blur-sm" @click="showDonationModal = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>
        
        <div class="relative w-[940px] max-w-[96vw] max-h-[92vh] overflow-y-auto paper-card rounded-4xl shadow-lift border border-cream-300/60"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-8"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-8">
            
            <button @click="showDonationModal = false" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-cream-100 hover:bg-cream-200 text-ink-700 text-[18px] flex items-center justify-center z-10 transition-colors">
                &times;
            </button>
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
                <div class="lg:col-span-5 p-7 border-b lg:border-b-0 lg:border-r border-cream-300/50">
                    <div class="photo bg-sun-200 photo-vignette rounded-3xl h-[280px] w-full relative overflow-hidden">
                        @if($animal->photo_path)
                            <img src="{{ Storage::url($animal->photo_path) }}" alt="{{ $animal->name }}" class="w-full h-full object-cover mix-blend-multiply opacity-80" />
                        @endif
                        <span class="placeholder-mono absolute">{{ mb_strtolower($animal->name) }}</span>
                    </div>
                    <div class="mt-4 paper-note rounded-2xl p-4 shadow-note note-tilt-1">
                        <div class="font-hand text-[20px] text-ink-700 leading-[1.25]">"Bugün ilk kez elimden mama yedi. Minicik bir an, ama gözü ışıldı."</div>
                        <div class="text-[10.5px] text-ink-700/55 mt-2 uppercase tracking-wider font-medium">— CAMİLLE · KORUYUCU AİLE</div>
                    </div>
                    <div class="mt-4 flex items-center gap-2 text-[12px] text-ink-700/65 px-2">
                        <svg class="w-3.5 h-3.5" aria-hidden="true"><use href="#lock" stroke="currentColor" fill="none"/></svg>
                        <span>256-bit şifreli · bağış belgesi otomatik gelir</span>
                    </div>
                </div>
                
                <div class="lg:col-span-7 p-8 flex flex-col">
                    <div class="text-[11px] uppercase tracking-[0.16em] text-clay-500 font-medium">
                        {{ mb_strtoupper($animal->name) }}'NİN ŞİFASINA ORTAK OL
                    </div>
                    <h2 class="font-serif text-[44px] text-ink-900 leading-[0.96] mt-1">Bir adım <em class="italic text-clay-500">hediye et</em>.</h2>
                    <p class="mt-3 text-[14.5px] text-ink-700/75 leading-relaxed">
                        Sevdiğin tutarı seç — her bağış doğrudan {{ $animal->name }}'nin günlük bakımına gider. 
                        Bağış sonrası adına bir "İyileştirici Mührü" hazırlanır.
                    </p>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 mt-6">
                        <template x-for="p in presets" :key="p.v">
                            <button @click="donationAmount = p.v" 
                                    :class="donationAmount === p.v ? 'bg-clay-500 text-cream-50 border-clay-500 shadow-card' : 'bg-cream-50 hover:bg-cream-100 border-cream-300/60 text-ink-700'"
                                    class="rounded-2xl py-4 text-center transition-all border">
                                <div class="font-serif text-[22px]">₺<span x-text="p.v"></span></div>
                                <div class="text-[10.5px] mt-1 opacity-75" x-text="p.label"></div>
                            </button>
                        </template>
                    </div>

                    <div class="mt-4 rounded-2xl bg-cream-50 border border-cream-300/60 p-4">
                        <label class="text-[11px] uppercase tracking-[0.16em] text-clay-500 font-medium block">Kendi tutarın</label>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="font-serif text-[36px] text-ink-900 leading-none">₺</span>
                            <input type="number" x-model.number="donationAmount" class="flex-1 bg-transparent outline-none font-serif text-[36px] text-ink-900 leading-none" min="1" />
                        </div>
                    </div>

                    <label class="flex items-center gap-3 mt-5 cursor-pointer">
                        <input type="checkbox" x-model="isRecurring" class="accent-sage-600 w-4 h-4 rounded border-cream-300" />
                        <span class="text-[13.5px] text-ink-700">
                            Her ay aynı tutar — <em class="italic text-sage-700">{{ $animal->name }}</em> için "kucağında bir koltuk".
                        </span>
                    </label>

                    <form action="{{ route('donate') }}" method="GET" class="mt-6 mt-auto">
                        <input type="hidden" name="animal_id" value="{{ $animal->id }}">
                        <input type="hidden" name="amount" :value="donationAmount">
                        <input type="hidden" name="recurring" :value="isRecurring ? 1 : 0">
                        <button type="submit" class="w-full rounded-full font-medium flex items-center justify-center gap-2 transition-colors px-6 py-3.5 text-[15px] bg-ink-900 hover:bg-ink-800 text-cream-50 shadow-card">
                            <svg class="w-4 h-4" aria-hidden="true"><use href="#lock" stroke="currentColor" fill="none"/></svg>
                            <span><span x-text="'₺' + donationAmount"></span> <span x-text="isRecurring ? 'aylık' : 'tek seferlik'"></span> · Ödemeye geç</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</div>
