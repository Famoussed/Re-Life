<div class="max-w-[1000px]">
    <div class="text-[12px] uppercase tracking-[0.16em] text-clay-500 font-medium">Platform Yönetimi</div>
    <h1 class="font-modern text-[40px] sm:text-[48px] leading-[0.95] text-ink-900 mt-1">Platform Genel Bakışı</h1>
    <p class="text-[15px] text-ink-700/75 mt-2 max-w-[560px]">
        Tüm barınakların, bağışçıların ve bağışların kuş bakışı özeti.
    </p>

    @if(session('status'))
        <div class="paper-note rounded-2xl shadow-note border border-sage-200 mt-5 px-5 py-3 text-[14px] text-sage-700">
            {{ session('status') }}
        </div>
    @endif

    @if($pendingShelters > 0)
        <div class="paper-note rounded-2xl shadow-note border border-sun-200 mt-5 px-5 py-4 flex items-center justify-between gap-4">
            <div class="text-[14px] text-ink-800">
                <span class="font-modern text-[22px] text-clay-500">{{ $pendingShelters }}</span>
                barınak onayınızı bekliyor.
            </div>
            <a href="{{ route('superadmin.approvals') }}"
               class="rounded-full bg-sage-600 hover:bg-sage-700 text-cream-50 px-5 py-2.5 text-[14px] font-medium shrink-0">
                Onayları gör
            </a>
        </div>
    @endif

    {{-- İstatistik kartları --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
        @php
            $stats = [
                ['Toplam Barınak', number_format($totalShelters, 0, ',', '.'), '#leaf'],
                ['Onaylı Barınak', number_format($approvedShelters, 0, ',', '.'), '#paw'],
                ['Onay Bekleyen', number_format($pendingShelters, 0, ',', '.'), '#sun'],
                ['Bağışçı Sayısı', number_format($totalUsers, 0, ',', '.'), '#heart'],
                ['Toplam Hayvan', number_format($totalAnimals, 0, ',', '.'), '#paw'],
                ['Toplam Bağış Tutarı', '₺'.number_format($totalDonationAmount, 0, ',', '.'), '#heart'],
                ['Toplam Bağış Adedi', number_format($totalDonationCount, 0, ',', '.'), '#sun'],
            ];
        @endphp
        @foreach($stats as [$label, $value, $icon])
            <div class="paper-note rounded-3xl shadow-note border border-cream-300/60 p-5">
                <div class="flex items-center gap-2 text-clay-500">
                    <svg class="w-4 h-4"><use href="{{ $icon }}" fill="currentColor"/></svg>
                    <span class="text-[12px] uppercase tracking-[0.12em] font-medium">{{ $label }}</span>
                </div>
                <div class="font-modern text-[30px] text-ink-900 mt-2 leading-none">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    {{-- En çok bağış toplayan barınaklar --}}
    <h2 class="font-modern text-[26px] text-ink-900 mt-10">En Çok Bağış Toplayan Barınaklar</h2>
    <div class="paper-card rounded-4xl shadow-card border border-cream-300/50 mt-4 overflow-hidden">
        @forelse($topShelters as $i => $row)
            <div class="flex items-center gap-4 px-5 sm:px-6 py-3.5 {{ $i > 0 ? 'border-t border-dashed border-clay-200' : '' }}">
                <div class="w-9 text-center font-modern text-[22px] {{ $i < 3 ? 'text-clay-500' : 'text-ink-700/40' }}">
                    {{ $i + 1 }}
                </div>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('shelters.show', $row['shelter']) }}"
                       class="font-medium text-[15px] text-ink-900 hover:text-sage-700">
                        {{ $row['shelter']->name }}
                    </a>
                    <div class="text-[12px] text-ink-700/60">
                        {{ $row['shelter']->city }} · {{ $row['count'] }} bağış
                    </div>
                </div>
                <div class="font-modern text-[20px] text-ink-900">₺{{ number_format($row['total'], 0, ',', '.') }}</div>
            </div>
        @empty
            <div class="p-12 text-center font-modern text-[24px] text-clay-500">
                henüz hiç bağış kaydı yok 🌱
            </div>
        @endforelse
    </div>
</div>
