@extends('store.layouts.app')

@section('title', __('store.order_placed'))

@section('content')
@php
    $locale = app()->getLocale();
@endphp
<section class="section-pad !py-16 max-w-3xl">
    <div class="text-center">
        <h1 class="font-display text-4xl font-bold">{{ __('store.order_placed') }}</h1>
        <p class="mt-4 text-lg">{{ __('store.order_number') }}: <strong class="text-[var(--color-copper-bright)]">{{ $order->order_number }}</strong></p>
        <p class="mt-2 text-[var(--color-ink-muted)]">{{ __('store.payment_pending') }}</p>
    </div>

    <div class="surface mt-8 border-[var(--color-copper)]/40 p-6 text-start text-sm">
        <div class="font-semibold text-[var(--color-copper-bright)]">{{ __('store.payment_guarantee_title') }}</div>
        <p class="mt-2 leading-relaxed text-[var(--color-ink-muted)]">{{ __('store.payment_guarantee_body') }}</p>
        <ul class="mt-4 list-disc space-y-2 ps-5 text-[var(--color-ink-muted)]">
            <li>{{ __('store.payment_step_1') }}</li>
            <li>{{ __('store.payment_step_2', ['order' => $order->order_number]) }}</li>
            <li>{{ __('store.payment_step_3') }}</li>
            <li>{{ __('store.payment_step_4') }}</li>
        </ul>
    </div>

    <div class="surface mt-6 p-6 text-start text-sm">
        <div class="font-semibold">{{ __('store.bank_transfer') }}</div>
        <p class="mt-2 whitespace-pre-line text-[var(--color-ink-muted)]">{{ $instructions }}</p>

        <dl class="mt-6 grid gap-3 border-t border-white/10 pt-4 sm:grid-cols-2">
            <div>
                <dt class="text-xs uppercase tracking-wide text-[var(--color-ink-muted)]">{{ __('store.bank_name') }}</dt>
                <dd class="mt-1 font-medium">{{ $locale === 'ar' ? $bank['name_ar'] : $bank['name_en'] }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-[var(--color-ink-muted)]">{{ __('store.account_name') }}</dt>
                <dd class="mt-1 font-medium">{{ $locale === 'ar' ? $bank['account_name_ar'] : $bank['account_name_en'] }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-[var(--color-ink-muted)]">{{ __('store.account_number') }}</dt>
                <dd class="mt-1 font-mono font-medium tracking-wide">{{ $bank['account_number'] }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-[var(--color-ink-muted)]">IBAN</dt>
                <dd class="mt-1 break-all font-mono font-medium tracking-wide text-[var(--color-copper-bright)]">{{ $bank['iban'] }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-[var(--color-ink-muted)]">SWIFT</dt>
                <dd class="mt-1 font-mono font-medium tracking-wide">{{ $bank['swift'] }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-[var(--color-ink-muted)]">{{ __('store.payment_reference') }}</dt>
                <dd class="mt-1 font-mono font-semibold tracking-wide">{{ $order->order_number }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs uppercase tracking-wide text-[var(--color-ink-muted)]">{{ __('store.amount_due') }}</dt>
                <dd class="mt-1 text-2xl font-semibold text-[var(--color-copper-bright)]">{{ number_format($order->total, 2) }} {{ __('store.sar') }}</dd>
            </div>
        </dl>
    </div>

    <div class="surface mt-6 p-6 text-start text-sm">
        <div class="font-semibold">{{ __('store.upload_receipt') }}</div>
        <p class="mt-2 text-[var(--color-ink-muted)]">{{ __('store.upload_receipt_help') }}</p>

        @if($payment?->receipt_path)
            <p class="mt-4 text-emerald-300">{{ __('store.receipt_uploaded') }}</p>
            <a class="mt-2 inline-block text-[var(--color-copper-bright)] hover:underline" href="{{ $payment->receiptUrl() }}" target="_blank">{{ __('store.view_receipt') }}</a>
        @endif

        <form method="post" action="{{ route('store.checkout.receipt', $order) }}" enctype="multipart/form-data" class="mt-4 space-y-3">
            @csrf
            <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.webp,.pdf" class="field" required>
            <button class="btn-primary">{{ __('store.submit_receipt') }}</button>
        </form>
    </div>

    <div class="mt-8 text-center">
        <a href="{{ route('store.home') }}" class="btn-secondary">{{ __('store.home') }}</a>
    </div>
</section>
@endsection
