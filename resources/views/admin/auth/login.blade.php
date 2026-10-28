<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('admin.login') }} — Smart Line</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grain flex min-h-screen items-center justify-center px-4">
    <form method="post" action="{{ route('admin.login.submit') }}" class="surface w-full max-w-md p-8">
        @csrf
        <div class="font-display text-3xl font-bold">Smart Line</div>
        <p class="mt-1 text-sm text-[var(--color-ink-soft)]">{{ __('admin.login') }}</p>
        <div class="mt-4 rounded-sm border border-[var(--color-sand-deep)] bg-[var(--color-mist)] px-3 py-3 text-xs text-[var(--color-ink-soft)]">
            <div class="font-semibold text-[var(--color-ink)]">{{ __('admin.default_login_title') }}</div>
            <div class="mt-1">{{ __('admin.email') }}: <code dir="ltr">admin@smartlinesa.net</code></div>
            <div>{{ __('admin.password') }}: <code dir="ltr">SmartLine@2026</code></div>
            <div class="mt-1">{{ __('admin.default_login_change_hint') }}</div>
        </div>
        @if($errors->any())
            <div class="mt-4 rounded-sm border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">{{ $errors->first() }}</div>
        @endif
        <div class="mt-6">
            <label class="label">{{ __('admin.email') }}</label>
            <input type="email" name="email" value="{{ old('email', 'admin@smartlinesa.net') }}" class="field" required>
        </div>
        <div class="mt-4">
            <label class="label">{{ __('admin.password') }}</label>
            <input type="password" name="password" class="field" required>
        </div>
        <label class="mt-4 flex items-center gap-2 text-sm">
            <input type="checkbox" name="remember" value="1"> {{ __('admin.remember') }}
        </label>
        <button class="btn-primary mt-6 w-full">{{ __('admin.login') }}</button>
    </form>
</body>
</html>
