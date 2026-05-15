@props(['tone' => 'sage', 'label' => null, 'path' => null])

<div {{ $attributes->merge(['class' => 'photo photo-vignette '.$tone]) }}
     @if($path) style="background-image:url('{{ \Illuminate\Support\Facades\Storage::url($path) }}')" @endif>
    @unless($path)
        @if($label)
            <span class="placeholder-mono">{{ $label }}</span>
        @endif
    @endunless
</div>
