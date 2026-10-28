@extends('store.layouts.app')

@section('title', __('store.checkout'))

@section('content')
<section class="section-pad !py-12">
    <h1 class="font-display text-4xl font-bold">{{ __('store.checkout') }}</h1>
    <p class="mt-2 text-sm text-[var(--color-ink-soft)]">{{ __('store.bank_transfer') }} — {{ __('store.payment_pending') }}</p>

    <form method="post" action="{{ route('store.checkout.store') }}" class="mt-8 grid gap-8 lg:grid-cols-[1.4fr_0.8fr]">
        @csrf
        <div class="surface space-y-4 p-6">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="label">{{ __('store.first_name') }}</label>
                    <input name="first_name" value="{{ old('first_name') }}" class="field" required>
                </div>
                <div>
                    <label class="label">{{ __('store.last_name') }}</label>
                    <input name="last_name" value="{{ old('last_name') }}" class="field" required>
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="label">{{ __('store.email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="field" required>
                </div>
                <div>
                    <label class="label">{{ __('store.phone') }}</label>
                    <input name="phone" value="{{ old('phone') }}" class="field">
                </div>
            </div>
            <div>
                <label class="label">{{ __('store.address') }}</label>
                <input name="address_line_1" value="{{ old('address_line_1') }}" class="field" required>
            </div>
            <div>
                <input name="address_line_2" value="{{ old('address_line_2') }}" class="field" placeholder="{{ __('store.address') }} 2">
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="label">{{ __('store.city') }}</label>
                    <input name="city" value="{{ old('city') }}" class="field" required>
                </div>
                <div>
                    <label class="label">{{ __('store.state') }}</label>
                    <input name="state" value="{{ old('state') }}" class="field">
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="label">{{ __('store.postal_code') }}</label>
                    <input name="postal_code" value="{{ old('postal_code') }}" class="field">
                </div>
                <div>
                    <label class="label">{{ __('store.country') }}</label>
                    <select name="country_code" class="field" required>
                        @foreach($countries as $country)
                            <option value="{{ $country->code }}" @selected(old('country_code', 'SA') === $country->code)>{{ $country->localizedName() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="label">{{ __('store.notes') }}</label>
                <textarea name="customer_notes" rows="3" class="field">{{ old('customer_notes') }}</textarea>
            </div>
            <button class="btn-primary">{{ __('store.place_order') }}</button>
        </div>

        <aside class="space-y-4">
            <div class="surface h-fit p-6">
                <h2 class="font-display text-xl font-semibold">{{ __('store.total') }}</h2>
                <div class="mt-4 space-y-2 text-sm">
                    @foreach($cart->items as $item)
                        <div class="flex justify-between gap-3">
                            <span>{{ $item->product?->name }} × {{ $item->quantity }}</span>
                            <span>{{ number_format($item->total_price, 2) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 border-t border-white/10 pt-4 text-lg font-semibold">
                    {{ number_format($cart->subtotal, 2) }} {{ __('store.sar') }}
                </div>
                <p class="mt-3 text-xs text-[var(--color-ink-muted)]">+ {{ __('store.tax') }} 15%</p>
            </div>
            <div class="surface border-[var(--color-copper)]/40 p-5 text-sm">
                <div class="font-semibold text-[var(--color-copper-bright)]">{{ __('store.payment_guarantee_title') }}</div>
                <p class="mt-2 text-[var(--color-ink-muted)]">{{ __('store.checkout_payment_note') }}</p>
                <p class="mt-3 font-mono text-xs text-[var(--color-ink)]">IBAN: {{ $bank['iban'] }}</p>
            </div>
        </aside>
    </form>
</section>
@endsection
