@php
    $image = $product->primaryImage();
    $visual = $image?->url() ?? ($fallback ?? null);
@endphp
<article class="product-card group">
    <div class="product-media aspect-[4/5]">
        @if($visual)
            <img src="{{ $visual }}" alt="{{ $product->name }}" loading="lazy" decoding="async" width="900" height="1125">
            <button
                type="button"
                class="zoom-hit"
                data-lightbox="product-{{ $product->id }}"
                data-lightbox-src="{{ $visual }}"
                data-lightbox-alt="{{ $product->name }}"
                aria-label="{{ __('store.view_all') }}: {{ $product->name }}"
            >
                <span class="gallery-zoom-plus" aria-hidden="true">+</span>
            </button>
        @else
            <div class="flex h-full items-center justify-center px-6 text-center text-sm font-medium text-[var(--color-ink-muted)]">
                {{ $product->name }}
            </div>
        @endif
    </div>
    <a href="{{ route('store.products.show', $product) }}" class="block p-4">
        <div class="inline-flex items-center gap-2 text-[10px] uppercase tracking-[0.28em] text-[var(--color-primary)]">
            @include('store.partials.icon', ['name' => 'products'])
            {{ $product->sku }} · {{ __('store.'.$product->type) }}
        </div>
        <h3 class="font-display mt-2 text-xl text-[var(--color-dark)]">{{ $product->name }}</h3>
        <div class="mt-3 flex items-end justify-between gap-3">
            <div>
                @if($product->type === 'custom_made' || (float) $product->base_price <= 0)
                    <div class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--color-primary)]">
                        @include('store.partials.icon', ['name' => 'quote'])
                        {{ __('store.request_quote') }}
                    </div>
                @else
                    @if($product->isOnSale())
                        <div class="text-xs text-[var(--color-ink-muted)] line-through">{{ number_format($product->base_price, 2) }} {{ __('store.sar') }}</div>
                    @endif
                    <div class="text-base font-semibold text-[var(--color-dark)]">{{ number_format($product->currentPrice(), 2) }} {{ __('store.sar') }}</div>
                @endif
            </div>
            <span class="inline-flex items-center gap-1 text-xs font-bold uppercase tracking-wider text-[var(--color-primary)]">
                @include('store.partials.icon', ['name' => 'bag'])
                {{ __('store.view_all') }}
            </span>
        </div>
    </a>
</article>
