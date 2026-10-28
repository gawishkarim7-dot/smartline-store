@extends('store.layouts.app')

@section('title', $product->name)

@section('content')
@php
    $visual = $product->primaryImage()?->url() ?? asset('images/brand/opt/project-'.((($product->id - 1) % 8) + 1).'.webp');
@endphp
<section class="section-pad !py-12">
    <div class="grid gap-10 lg:grid-cols-2">
        <div class="product-media aspect-[4/5] lg:aspect-[4/4.5]">
            @include('store.partials.zoom-image', [
                'src' => $visual,
                'alt' => $product->name,
                'class' => 'absolute inset-0 h-full w-full',
                'imgClass' => 'h-full w-full object-cover',
                'width' => 900,
                'height' => 900,
                'loading' => 'eager',
                'group' => 'product-'.$product->id,
            ])
        </div>

        <div>
            <div class="section-kicker">{{ __('store.'.$product->type) }}</div>
            <h1 class="font-display mt-3 text-4xl font-bold sm:text-5xl">{{ $product->name }}</h1>
            <p class="mt-4 text-[var(--color-ink-muted)]">{{ $product->description }}</p>

            <div class="mt-8 flex flex-wrap items-end gap-4">
                <div>
                    @if($product->type === 'custom_made' || (float) $product->base_price <= 0)
                        <div class="text-2xl font-semibold text-[var(--color-copper-bright)]">{{ __('store.request_quote') }}</div>
                        <p class="mt-1 text-sm text-[var(--color-ink-muted)]">{{ __('store.custom_price_note') }}</p>
                    @else
                        @if($product->isOnSale())
                            <div class="text-sm text-[var(--color-ink-muted)] line-through">{{ number_format($product->base_price, 2) }} {{ __('store.sar') }}</div>
                        @endif
                        <div class="text-4xl font-semibold text-[var(--color-copper-bright)]">{{ number_format($product->currentPrice(), 2) }} {{ __('store.sar') }}</div>
                    @endif
                </div>
                <div class="text-sm text-[var(--color-ink-muted)]">
                    {{ __('store.lead_time') }}: {{ $product->lead_time_days }} {{ __('store.days') }}
                </div>
            </div>

            @if($product->specifications)
                <div class="surface mt-8 p-5 text-sm">
                    <div class="font-semibold text-[var(--color-copper-bright)]">{{ __('store.specifications') }}</div>
                    <p class="mt-2 text-[var(--color-ink-muted)]">{{ $product->specifications }}</p>
                </div>
            @endif

            <div class="mt-8 flex flex-wrap gap-3">
                @if($product->allowsDirectPurchase())
                    <form method="post" action="{{ route('store.cart.store') }}" class="flex flex-wrap items-center gap-3">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="number" name="quantity" value="1" min="1" max="100" class="field w-24">
                        <button class="btn-primary">
                            @include('store.partials.icon', ['name' => 'cart']) {{ __('store.add_to_cart') }}
                        </button>
                    </form>
                @endif
                @if($product->requiresQuote())
                    <a href="{{ route('store.quote.create', ['product' => $product->id]) }}" class="btn-outline">
                        @include('store.partials.icon', ['name' => 'quote']) {{ __('store.request_quote') }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    @if($related->isNotEmpty())
        <div class="mt-20">
            <h2 class="font-display text-3xl font-bold">{{ __('store.products') }}</h2>
            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($related as $item)
                    @include('store.partials.product-card', ['product' => $item, 'fallback' => asset('images/brand/opt/project-'.((($item->id - 1) % 8) + 1).'.webp')])
                @endforeach
            </div>
        </div>
    @endif
</section>
@endsection
