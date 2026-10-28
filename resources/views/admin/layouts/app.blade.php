<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('admin.dashboard')) — Smart Line</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[var(--color-mist)] text-[var(--color-ink)]">
    <div class="min-h-screen md:grid md:grid-cols-[240px_1fr]">
        <aside class="border-e border-[var(--color-sand-deep)] bg-[var(--color-steel-dark)] text-[var(--color-sand)]">
            <div class="border-b border-white/10 px-5 py-5">
                <div class="font-display text-xl font-bold text-white">Smart Line</div>
                <div class="text-xs uppercase tracking-wider text-[var(--color-sand-deep)]">Admin</div>
            </div>
            <nav class="flex flex-col gap-1 p-3 text-sm">
                <a class="rounded-sm px-3 py-2 hover:bg-white/10" href="{{ route('admin.dashboard') }}">{{ __('admin.dashboard') }}</a>
                <a class="rounded-sm px-3 py-2 hover:bg-white/10" href="{{ route('admin.products.index') }}">{{ __('admin.products') }}</a>
                <a class="rounded-sm px-3 py-2 hover:bg-white/10" href="{{ route('admin.categories.index') }}">{{ __('admin.categories') }}</a>
                <a class="rounded-sm px-3 py-2 hover:bg-white/10" href="{{ route('admin.orders.index') }}">{{ __('admin.orders') }}</a>
                <a class="rounded-sm px-3 py-2 hover:bg-white/10" href="{{ route('admin.quotes.index') }}">{{ __('admin.quote_requests') }}</a>
                <a class="rounded-sm px-3 py-2 hover:bg-white/10" href="{{ route('admin.profile.edit') }}">{{ __('admin.account_settings') }}</a>
                <a class="rounded-sm px-3 py-2 hover:bg-white/10" href="{{ route('store.home') }}">{{ __('admin.view_store') }}</a>
                <form method="post" action="{{ route('admin.logout') }}" class="mt-4">
                    @csrf
                    <button class="w-full rounded-sm px-3 py-2 text-start hover:bg-white/10">{{ __('admin.logout') }}</button>
                </form>
            </nav>
        </aside>
        <div>
            <header class="flex items-center justify-between border-b border-[var(--color-sand-deep)] bg-white px-6 py-4">
                <h1 class="font-display text-xl font-semibold">@yield('title', __('admin.dashboard'))</h1>
                <a href="{{ route('admin.profile.edit') }}" class="text-sm text-[var(--color-ink-soft)] hover:text-[var(--color-primary)]">
                    {{ auth()->user()?->name }} · {{ __('admin.change_password') }}
                </a>
            </header>
            <main class="p-6">
                @if(session('success'))
                    <div class="mb-4 rounded-sm border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('success') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
