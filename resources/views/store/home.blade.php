@extends('store.layouts.app')

@section('title', __('store.brand'))

@section('content')
@php
    $projectImages = collect(range(1, 6))->map(fn ($n) => asset("images/brand/opt/project-{$n}.webp"))->all();
    $categoryVisuals = [
        'metal-works' => asset('images/brand/opt/project-2.webp'),
        'wood-works' => asset('images/brand/opt/project-6.webp'),
        'cabinets' => asset('images/brand/opt/project-8.webp'),
        'custom-doors' => asset('images/brand/opt/project-3.webp'),
        'fences' => asset('images/brand/opt/project-5.webp'),
        'hand-rails' => asset('images/brand/opt/project-4.webp'),
        'furniture' => asset('images/brand/opt/project-7.webp'),
        'louver-units' => asset('images/brand/opt/project-1.webp'),
    ];
    $services = [
        ['icon' => 'design', 'title' => __('store.service_design'), 'body' => __('store.service_design_body')],
        ['icon' => 'build', 'title' => __('store.service_fabricate'), 'body' => __('store.service_fabricate_body')],
        ['icon' => 'check', 'title' => __('store.service_install'), 'body' => __('store.service_install_body')],
        ['icon' => 'metal', 'title' => __('store.service_quality'), 'body' => __('store.service_quality_body')],
    ];
@endphp

<section class="hero-stage">
    <div class="hero-media" aria-hidden="true">
        <img
            src="{{ asset('images/brand/opt/hero.webp') }}"
            alt=""
            width="1600"
            height="1066"
            fetchpriority="high"
            decoding="async"
        >
    </div>
    <div class="hero-veil" aria-hidden="true"></div>

    <div class="mx-auto w-full max-w-4xl px-4 py-24 sm:px-6 lg:px-8">
        <div class="font-display text-4xl uppercase tracking-[0.08em] text-white sm:text-5xl lg:text-6xl">Smart Line</div>
        <div class="hero-line mt-6"></div>
        <h1 class="font-display mt-6 text-3xl uppercase leading-[1.15] text-white sm:text-4xl lg:text-5xl">
            {{ __('store.hero_title') }}
        </h1>
        <p class="mx-auto mt-5 max-w-2xl text-base leading-relaxed text-white/90 sm:text-lg">
            {{ __('store.hero_subtitle') }}
        </p>
        <div class="mt-9 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('store.products.index') }}" class="btn-secondary">
                @include('store.partials.icon', ['name' => 'products']) {{ __('store.shop_products') }}
            </a>
            <a href="{{ route('store.quote.create') }}" class="btn-secondary">
                @include('store.partials.icon', ['name' => 'quote']) {{ __('store.request_quote') }}
            </a>
        </div>
    </div>
</section>

<section class="bg-[var(--color-light)]">
    <div class="section-pad !py-14">
        <div class="mb-10 section-title-bar">
            <div class="section-kicker">{{ __('store.our_services') }}</div>
            <h2 class="font-display mt-2 text-3xl sm:text-4xl">{{ __('store.services_title') }}</h2>
        </div>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($services as $service)
                <article class="service-card">
                    <div class="icon-wrap">
                        @include('store.partials.icon', ['name' => $service['icon'], 'class' => 'icon icon-lg'])
                    </div>
                    <h3 class="font-display text-xl">{{ $service['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed">{{ $service['body'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="section-pad">
    <div class="mb-10 section-title-bar">
        <div class="section-kicker">{{ __('store.categories') }}</div>
        <h2 class="font-display mt-2 text-3xl sm:text-4xl">{{ __('store.work_metals_wood') }}</h2>
    </div>

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($categories as $index => $category)
            @php $visual = $categoryVisuals[$category->slug] ?? $projectImages[$index % count($projectImages)]; @endphp
            <a href="{{ route('store.products.index', ['category' => $category->slug]) }}" class="category-tile group p-5">
                <img src="{{ $visual }}" alt="{{ $category->name }}" loading="lazy" decoding="async" width="900" height="675">
                <div>
                    <div class="inline-flex items-center gap-2 text-[11px] uppercase tracking-[0.28em] text-[var(--color-primary-soft)]">
                        @include('store.partials.icon', ['name' => str_contains($category->slug, 'wood') || str_contains($category->name, 'خشب') ? 'wood' : 'metal'])
                        {{ __('store.categories') }}
                    </div>
                    <h3 class="font-display mt-2 text-2xl text-white">{{ $category->name }}</h3>
                    <p class="mt-2 line-clamp-2 text-sm text-white/75">{{ $category->description }}</p>
                </div>
            </a>
        @endforeach
    </div>
</section>

<section class="bg-[var(--color-light)]">
    <div class="section-pad">
        <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
            <div class="section-title-bar">
                <div class="section-kicker">{{ __('store.featured') }} · {{ $productsCount }} {{ __('store.products') }}</div>
                <h2 class="font-display mt-2 text-3xl sm:text-4xl">{{ __('store.products') }}</h2>
            </div>
            <a href="{{ route('store.products.index') }}" class="btn-outline">
                @include('store.partials.icon', ['name' => 'products']) {{ __('store.view_all') }} ({{ $productsCount }})
            </a>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($featured as $product)
                @php
                    $imgIndex = (crc32($product->sku) % count($projectImages) + count($projectImages)) % count($projectImages);
                @endphp
                @include('store.partials.product-card', ['product' => $product, 'fallback' => $projectImages[$imgIndex]])
            @endforeach
        </div>
    </div>
</section>

<section class="section-pad">
    <div class="mb-8 section-title-bar">
        <div class="section-kicker">Smart Line</div>
        <h2 class="font-display mt-2 text-3xl sm:text-4xl">{{ __('store.projects_title') }}</h2>
        <p class="mt-3 max-w-2xl">{{ __('store.projects_subtitle') }}</p>
    </div>
    <div class="project-rail">
        @foreach($projectImages as $image)
            <figure class="relative aspect-[4/5] overflow-hidden bg-[var(--color-light)]">
                @include('store.partials.zoom-image', [
                    'src' => $image,
                    'alt' => 'Smart Line project',
                    'class' => 'absolute inset-0 h-full w-full',
                    'imgClass' => 'h-full w-full object-cover',
                    'width' => 900,
                    'height' => 1125,
                    'group' => 'projects',
                ])
            </figure>
        @endforeach
    </div>
</section>

<section class="cta-band">
    <div class="absolute inset-0">
        <img src="{{ asset('images/brand/opt/project-8.webp') }}" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async">
        <div class="absolute inset-0" style="background: linear-gradient(rgba(131, 86, 60, 0.78), rgba(28, 27, 26, 0.85));"></div>
    </div>
    <div class="relative section-pad text-center">
        <div class="mx-auto max-w-2xl">
            <div class="section-kicker justify-center">{{ __('store.quote') }}</div>
            <h2 class="font-display mt-3 text-3xl sm:text-5xl">{{ __('store.quote_cta_title') }}</h2>
            <p class="mt-4 text-white/85">{{ __('store.quote_cta_body') }}</p>
            <a href="{{ route('store.quote.create') }}" class="btn-secondary mt-8">
                @include('store.partials.icon', ['name' => 'quote']) {{ __('store.request_quote') }}
            </a>
        </div>
    </div>
</section>
@endsection
