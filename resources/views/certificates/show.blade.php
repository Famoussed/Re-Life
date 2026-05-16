@php($anonymous = (bool) ($certificate->donation?->is_anonymous) && auth()->id() !== $certificate->user_id)

<x-app-layout>
    <div class="max-w-[1100px] mx-auto px-5 sm:px-8 py-8">

        <div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
            <div>
                <a href="{{ url()->previous() }}" class="text-[13px] text-sage-700 hover:text-sage-600">← Geri dön</a>
                <h1 class="font-modern text-[32px] text-ink-900 leading-tight mt-1">Teşekkür Belgesi</h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('certificates.pdf', $certificate) }}"
                   class="rounded-full bg-sage-600 hover:bg-sage-700 text-cream-50 px-5 py-2.5 text-[14px] font-medium">
                    PDF olarak indir
                </a>
                <button type="button"
                    x-data
                    x-on:click="
                        if (navigator.share) {
                            navigator.share({ title: 'Re·Life Teşekkür Belgem', url: window.location.href });
                        } else {
                            navigator.clipboard.writeText(window.location.href);
                            $el.textContent = 'Bağlantı kopyalandı';
                        }
                    "
                    class="rounded-full bg-cream-100 hover:bg-cream-200 border border-cream-300/60 text-ink-700 px-5 py-2.5 text-[14px]">
                    Paylaş
                </button>
            </div>
        </div>

        {{-- Sertifika önizleme --}}
        <div class="paper-card rounded-3xl border border-cream-300/60 shadow-card p-4 sm:p-6 overflow-x-auto">
            <div style="min-width: 1000px;">
                @include('certificates.partials.design', ['certificate' => $certificate, 'anonymous' => $anonymous])
            </div>
        </div>

    </div>
</x-app-layout>
