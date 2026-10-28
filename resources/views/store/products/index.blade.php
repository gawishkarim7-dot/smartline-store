@extends('store.layouts.app')

@section('title', __('store.products'))

@section('content')
@php
    $fallbacks = [
        asset('images/brand/opt/project-1.webp'),
        asset('images/brand/opt/project-2.webp'),
        asset('images/brand/opt/project-3.webp'),
        asset('images/brand/opt/project-4.webp'),
        asset('images/brand/opt/project-5.webp'),
        asset('images/brand/opt/project-6.webp'),
    ];
@endphp
<section class="section-pad !py-12">
    <div class="section-title-bar">
        <div class="section-kicker">
            @include('store.partials.icon', ['name' => 'products'])
            Smart Line · {{ $productsCount }} {{ __('store.products') }}
        </div>
        <h1 class="font-display mt-3 text-4xl sm:text-5xl">{{ __('store.products') }}</h1>
        <p class="mt-2 text-sm">{{ __('store.catalog_count', ['count' => $productsCount]) }}</p>
    </div>

    <form method="get" class="surface mt-8 grid gap-3 p-4 md:grid-cols-4">
        <label class="relative md:col-span-2">
            <span class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-[var(--color-primary)]">
                @include('store.partials.icon', ['name' => 'search'])
            </span>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('store.search_placeholder') }}" class="field ps-10">
        </label>
        <select name="category" class="field">
            <option value="">{{ __('store.categories') }}</option>
            @foreach($categories as $category)
                <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select name="type" class="field">
            <option value="">{{ __('store.all_types') }}</option>
            <option value="ready_made" @selected(request('type') === 'ready_made')>{{ __('store.ready_made') }}</option>
            <option value="custom_made" @selected(request('type') === 'custom_made')>{{ __('store.custom_made') }}</option>
            <option value="hybrid" @selected(request('type') === 'hybrid')>{{ __('store.hybrid') }}</option>
        </select>
        <select name="sort" class="field md:col-span-2">
            <option value="newest" @selected(request('sort', 'newest') === 'newest')>{{ __('store.newest') }}</option>
            <option value="price_low" @selected(request('sort') === 'price_low')>{{ __('store.price_low') }}</option>
            <option value="price_high" @selected(request('sort') === 'price_high')>{{ __('store.price_high') }}</option>
        </select>
        <button class="btn-primary md:col-span-2">
            @include('store.partials.icon', ['name' => 'filter']) {{ __('store.filter') }}
        </button>
    </form>

    @if($products->isEmpty())
        <p class="mt-10">{{ __('store.no_products') }}</p>
    @else
        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($products as $product)
                @php
                    $imgIndex = (crc32($product->sku) % count($fallbacks) + count($fallbacks)) % count($fallbacks);
                @endphp
                @include('store.partials.product-card', ['product' => $product, 'fallback' => $fallbacks[$imgIndex]])
            @endforeach
        </div>
        <div class="mt-8">{{ $products->links() }}</div>
    @endif
</section>
@endsection
