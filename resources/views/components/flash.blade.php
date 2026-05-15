@if (session('status'))
    <div class="mb-5 rounded-2xl bg-sage-100 border border-sage-200 text-sage-700 px-4 py-3 text-[14px]">
        {{ session('status') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-5 rounded-2xl bg-peach-100 border border-peach-200 text-clay-600 px-4 py-3 text-[14px]">
        {{ session('error') }}
    </div>
@endif
