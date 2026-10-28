@php
    $src = $src ?? '';
    $alt = $alt ?? '';
    $class = $class ?? '';
    $imgClass = $imgClass ?? 'h-full w-full object-cover';
    $width = $width ?? null;
    $height = $height ?? null;
    $loading = $loading ?? 'lazy';
    $group = $group ?? 'gallery';
@endphp
<a
    href="{{ $src }}"
    class="gallery-zoom {{ $class }}"
    data-lightbox="{{ $group }}"
    data-lightbox-src="{{ $src }}"
    data-lightbox-alt="{{ $alt }}"
    aria-label="{{ $alt !== '' ? $alt : __('store.view_all') }}"
>
    <img
        src="{{ $src }}"
        alt="{{ $alt }}"
        class="{{ $imgClass }}"
        loading="{{ $loading }}"
        decoding="async"
        @if($width) width="{{ $width }}" @endif
        @if($height) height="{{ $height }}" @endif
    >
    <span class="gallery-zoom-overlay" aria-hidden="true">
        <span class="gallery-zoom-plus">+</span>
    </span>
</a>
