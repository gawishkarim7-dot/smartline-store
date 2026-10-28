<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('store.brand')) — {{ __('store.tagline') }}</title>
    <link rel="icon" href="{{ asset('images/brand/logo-corporate.jpg') }}">
    <link rel="preload" as="image" href="{{ asset('images/brand/opt/hero.webp') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="site-shell min-h-screen">
    @php
        $switchLocale = app()->getLocale() === 'ar' ? 'en' : 'ar';
        $cartCount = app(\App\Services\CartService::class)->itemCount();
        $routeName = request()->route()?->getName() ?? '';
        $companyWebsite = config('store.company_website');
    @endphp

    <div class="topbar hidden lg:block">
        <div class="mx-auto grid max-w-7xl gap-4 px-4 py-3 sm:px-6 md:grid-cols-3 lg:px-8">
            <div class="topbar-item">
                @include('store.partials.icon', ['name' => 'location', 'class' => 'icon'])
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-[var(--color-dark)]">{{ __('store.office') }}</div>
                    <div>{{ app()->getLocale() === 'ar' ? config('store.contact.address_ar') : config('store.contact.address') }}</div>
                </div>
            </div>
            <div class="topbar-item md:justify-center">
                @include('store.partials.icon', ['name' => 'email', 'class' => 'icon'])
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-[var(--color-dark)]">{{ __('store.email') }}</div>
                    <a href="mailto:{{ config('store.contact.email') }}">{{ config('store.contact.email') }}</a>
                </div>
            </div>
            <div class="topbar-item md:justify-end">
                @include('store.partials.icon', ['name' => 'phone', 'class' => 'icon'])
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-[var(--color-dark)]">{{ __('store.phone') }}</div>
                    <a href="tel:+966592458908">{{ config('store.contact.phone') }}</a>
                </div>
            </div>
        </div>
    </div>

    <header id="site-header" class="header-glass sticky top-0 z-50">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('store.home') }}" class="shrink-0" aria-label="Smart Line">
                <img src="{{ asset('images/brand/logo-corporate.jpg') }}" alt="Smart Line — Metal and Wood" class="logo-mark" width="180" height="70" decoding="async">
            </a>

            <nav class="hidden items-center gap-6 lg:flex xl:gap-8">
                <a href="{{ route('store.home') }}" class="nav-link {{ $routeName === 'store.home' ? 'is-active' : '' }}">
                    @include('store.partials.icon', ['name' => 'home']) {{ __('store.home') }}
                </a>
                <a href="{{ route('store.products.index') }}" class="nav-link {{ str_starts_with($routeName, 'store.products') ? 'is-active' : '' }}">
                    @include('store.partials.icon', ['name' => 'products']) {{ __('store.products') }}
                </a>
                <a href="{{ route('store.quote.create') }}" class="nav-link {{ str_starts_with($routeName, 'store.quote') ? 'is-active' : '' }}">
                    @include('store.partials.icon', ['name' => 'quote']) {{ __('store.quote') }}
                </a>
                <a href="{{ route('store.page', 'about') }}" class="nav-link {{ request()->is('*/about') || request()->is('about') ? 'is-active' : '' }}">
                    @include('store.partials.icon', ['name' => 'about']) {{ __('store.about') }}
                </a>
                <a
                    href="{{ $companyWebsite }}"
                    class="nav-main-site"
                    target="_blank"
                    rel="noopener noreferrer"
                    title="{{ __('store.main_website_open') }}"
                >
                    @include('store.partials.icon', ['name' => 'globe']) {{ __('store.main_website') }}
                </a>
                <a href="{{ route('store.page', 'contact') }}" class="nav-contact">
                    @include('store.partials.icon', ['name' => 'contact']) {{ __('store.contact') }}
                </a>
            </nav>

            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route('store.products.index') }}" class="icon-btn" title="{{ __('store.products') }}" aria-label="{{ __('store.products') }}">
                    @include('store.partials.icon', ['name' => 'products', 'class' => 'icon'])
                </a>
                <a href="{{ route('store.cart.index') }}" class="icon-btn" title="{{ __('store.cart') }}" aria-label="{{ __('store.cart') }}">
                    @include('store.partials.icon', ['name' => 'cart', 'class' => 'icon'])
                    @if($cartCount > 0)
                        <span class="badge-count">{{ $cartCount }}</span>
                    @endif
                </a>
                <a href="{{ route('locale.switch', $switchLocale) }}" class="icon-btn" title="{{ __('store.language') }}" aria-label="{{ __('store.language') }}">
                    @include('store.partials.icon', ['name' => 'globe', 'class' => 'icon'])
                </a>
                <button type="button" id="nav-toggle" class="icon-btn lg:hidden" aria-expanded="false" aria-controls="mobile-nav">
                    <span class="sr-only">Menu</span>
                    @include('store.partials.icon', ['name' => 'menu', 'class' => 'icon'])
                </button>
            </div>
        </div>

        <div id="mobile-nav" class="hidden border-t border-[var(--color-sand-deep)] bg-white px-4 py-4 lg:hidden">
            <div class="flex flex-col gap-1 text-sm font-bold uppercase tracking-wide text-[var(--color-dark)]">
                <a class="flex items-center gap-3 px-3 py-3 hover:bg-[var(--color-light)] hover:text-[var(--color-primary)]" href="{{ route('store.home') }}">
                    @include('store.partials.icon', ['name' => 'home']) {{ __('store.home') }}
                </a>
                <a class="flex items-center gap-3 px-3 py-3 hover:bg-[var(--color-light)] hover:text-[var(--color-primary)]" href="{{ route('store.products.index') }}">
                    @include('store.partials.icon', ['name' => 'products']) {{ __('store.products') }}
                </a>
                <a class="flex items-center gap-3 px-3 py-3 hover:bg-[var(--color-light)] hover:text-[var(--color-primary)]" href="{{ route('store.quote.create') }}">
                    @include('store.partials.icon', ['name' => 'quote']) {{ __('store.quote') }}
                </a>
                <a class="flex items-center gap-3 px-3 py-3 hover:bg-[var(--color-light)] hover:text-[var(--color-primary)]" href="{{ route('store.page', 'about') }}">
                    @include('store.partials.icon', ['name' => 'about']) {{ __('store.about') }}
                </a>
                <a
                    class="flex items-center gap-3 border-2 border-[var(--color-primary)] px-3 py-3 text-[var(--color-primary)]"
                    href="{{ $companyWebsite }}"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    @include('store.partials.icon', ['name' => 'globe']) {{ __('store.main_website') }}
                </a>
                <a class="flex items-center gap-3 bg-[var(--color-primary)] px-3 py-3 text-white" href="{{ route('store.page', 'contact') }}">
                    @include('store.partials.icon', ['name' => 'contact']) {{ __('store.contact') }}
                </a>
            </div>
        </div>
    </header>

    @if(session('success'))
        <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
            <div class="border border-emerald-500/30 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
            <div class="border border-red-500/30 bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-disc ps-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <div id="store-lightbox" class="lightbox" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Image preview">
        <div class="lightbox-dialog">
            <button type="button" id="store-lightbox-close" class="lightbox-close" aria-label="Close">&times;</button>
            <img id="store-lightbox-image" src="" alt="">
        </div>
    </div>

    <footer class="footer-site mt-10">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 md:grid-cols-[1.3fr_1fr_1fr] lg:px-8">
            <div>
                <img src="{{ asset('images/brand/opt/logo-white.webp') }}" alt="Smart Line" class="h-12 w-auto" width="180" height="52" loading="lazy" decoding="async">
                <p class="mt-5 max-w-md text-sm leading-relaxed">{{ __('store.hero_subtitle') }}</p>
            </div>
            <div class="text-sm">
                <div class="section-kicker">{{ __('store.office') }}</div>
                <p class="mt-4 flex items-start gap-2">
                    @include('store.partials.icon', ['name' => 'location'])
                    <span>{{ app()->getLocale() === 'ar' ? config('store.contact.address_ar') : config('store.contact.address') }}</span>
                </p>
                <p class="mt-3 flex items-center gap-2">
                    @include('store.partials.icon', ['name' => 'email'])
                    <a href="mailto:{{ config('store.contact.email') }}">{{ config('store.contact.email') }}</a>
                </p>
                <p class="mt-2 flex items-center gap-2">
                    @include('store.partials.icon', ['name' => 'phone'])
                    <a href="tel:+966592458908">{{ config('store.contact.phone') }}</a>
                </p>
            </div>
            <div class="text-sm">
                <div class="section-kicker">{{ __('store.products') }}</div>
                <div class="mt-4 flex flex-col gap-3">
                    <a class="inline-flex items-center gap-2" href="{{ $companyWebsite }}" target="_blank" rel="noopener noreferrer">
                        @include('store.partials.icon', ['name' => 'globe']) {{ __('store.main_website') }}
                    </a>
                    <a class="inline-flex items-center gap-2" href="{{ route('store.products.index') }}">
                        @include('store.partials.icon', ['name' => 'products']) {{ __('store.shop_products') }}
                    </a>
                    <a class="inline-flex items-center gap-2" href="{{ route('store.cart.index') }}">
                        @include('store.partials.icon', ['name' => 'cart']) {{ __('store.cart') }}
                    </a>
                    <a class="inline-flex items-center gap-2" href="{{ route('store.quote.create') }}">
                        @include('store.partials.icon', ['name' => 'quote']) {{ __('store.quote') }}
                    </a>
                    <a class="inline-flex items-center gap-2" href="{{ route('store.page', 'privacy') }}">{{ __('store.privacy') }}</a>
                    <a class="inline-flex items-center gap-2" href="{{ route('store.page', 'terms') }}">{{ __('store.terms') }}</a>
                </div>
            </div>
        </div>
        <div class="border-t border-white/10 py-4 text-center text-xs tracking-wide">
            © {{ date('Y') }} Smart Line — Metal and Wood
        </div>
    </footer>
</body>
</html>
