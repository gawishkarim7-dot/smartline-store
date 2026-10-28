@extends('admin.layouts.app')

@section('title', __('admin.account_settings'))

@section('content')
<div class="mx-auto max-w-2xl">
    <div class="surface p-6">
        <h2 class="font-display text-2xl font-semibold">{{ __('admin.account_settings') }}</h2>
        <p class="mt-2 text-sm text-[var(--color-ink-soft)]">{{ __('admin.account_settings_help') }}</p>

        <form method="post" action="{{ route('admin.profile.update') }}" class="mt-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="label">{{ __('admin.name') }}</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="field" required>
                @error('name') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">{{ __('admin.email') }}</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="field" required>
                @error('email') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>

            <div class="border-t border-[var(--color-sand-deep)] pt-4">
                <div class="text-sm font-semibold">{{ __('admin.change_password') }}</div>
                <p class="mt-1 text-xs text-[var(--color-ink-soft)]">{{ __('admin.change_password_help') }}</p>
            </div>

            <div>
                <label class="label">{{ __('admin.current_password') }}</label>
                <input type="password" name="current_password" class="field" autocomplete="current-password">
                @error('current_password') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">{{ __('admin.new_password') }}</label>
                <input type="password" name="password" class="field" autocomplete="new-password">
                @error('password') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">{{ __('admin.new_password_confirmation') }}</label>
                <input type="password" name="password_confirmation" class="field" autocomplete="new-password">
            </div>

            <button class="btn-primary">{{ __('admin.save') }}</button>
        </form>
    </div>

    <div class="surface mt-4 p-4 text-sm text-[var(--color-ink-soft)]">
        <div class="font-semibold text-[var(--color-ink)]">{{ __('admin.default_login_title') }}</div>
        <p class="mt-2">{{ __('admin.default_login_body') }}</p>
        <ul class="mt-2 list-disc ps-5">
            <li>{{ __('admin.email') }}: <code>admin@smartlinesa.net</code></li>
            <li>{{ __('admin.password') }}: <code>SmartLine@2026</code></li>
        </ul>
    </div>
</div>
@endsection
