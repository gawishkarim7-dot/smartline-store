@extends('admin.layouts.app')

@section('title', $order->order_number)

@section('content')
@php $payment = $order->payments->first(); @endphp
<div class="grid gap-6 lg:grid-cols-[1.4fr_0.8fr]">
    <div class="space-y-6">
        <div class="surface p-5 text-sm">
            <div><strong>{{ __('admin.customer') }}:</strong> {{ $order->customer_full_name }} · {{ $order->customer_email }} · {{ $order->customer_phone }}</div>
            <div class="mt-2"><strong>{{ __('admin.status') }}:</strong> {{ $order->status }}</div>
            <div class="mt-2"><strong>{{ __('admin.total') }}:</strong> {{ number_format($order->total, 2) }} {{ $order->currency }}</div>
            @if($payment)
                <div class="mt-2"><strong>{{ __('admin.payment_status') }}:</strong> {{ $payment->status }}</div>
                <div class="mt-2"><strong>{{ __('admin.payment_reference') }}:</strong> {{ $payment->payment_reference ?? $order->order_number }}</div>
            @endif
        </div>

        @if($payment)
            <div class="surface space-y-3 p-5 text-sm">
                <h3 class="font-semibold">{{ __('admin.bank_payment') }}</h3>
                <p class="text-[var(--color-ink-soft)]">{{ __('admin.bank_payment_help') }}</p>
                @if($payment->receipt_path)
                    <a class="inline-block font-medium text-[var(--color-copper)] hover:underline" href="{{ $payment->receiptUrl() }}" target="_blank">{{ __('admin.view_receipt') }}</a>
                    <div class="text-xs text-[var(--color-ink-soft)]">{{ $payment->receipt_uploaded_at }}</div>
                @else
                    <p class="text-[var(--color-ink-soft)]">{{ __('admin.no_receipt') }}</p>
                @endif

                @if($payment->status !== 'completed')
                    <form method="post" action="{{ route('admin.orders.confirm-payment', $order) }}" class="space-y-3 border-t border-[var(--color-sand-deep)] pt-4">
                        @csrf
                        <textarea name="notes" class="field" rows="2" placeholder="{{ __('admin.confirm_payment_notes') }}"></textarea>
                        <button class="btn-accent">{{ __('admin.confirm_payment') }}</button>
                    </form>
                @else
                    <p class="font-semibold text-emerald-700">{{ __('admin.payment_confirmed') }} — {{ $payment->paid_at }}</p>
                @endif
            </div>
        @endif

        <div class="surface overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-[var(--color-mist)]"><tr>
                    <th class="px-4 py-3 text-start">Item</th>
                    <th class="px-4 py-3 text-start">{{ __('admin.quantity') }}</th>
                    <th class="px-4 py-3 text-start">{{ __('admin.unit_price') }}</th>
                    <th class="px-4 py-3 text-start">{{ __('admin.line_total') }}</th>
                </tr></thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr class="border-t border-[var(--color-sand-deep)]">
                            <td class="px-4 py-3">{{ $item->product_name }} <span class="text-xs text-[var(--color-ink-soft)]">{{ $item->product_sku }}</span></td>
                            <td class="px-4 py-3">{{ $item->quantity }}</td>
                            <td class="px-4 py-3">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-4 py-3">{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="space-y-6">
        <form method="post" action="{{ route('admin.orders.status', $order) }}" class="surface space-y-3 p-5">
            @csrf
            @method('PUT')
            <label class="label">{{ __('admin.update_status') }}</label>
            <select name="status" class="field">
                @foreach(['pending_payment','payment_verification','confirmed','processing','manufacturing','ready','shipped','delivered','cancelled','completed'] as $status)
                    <option value="{{ $status }}" @selected($order->status === $status)>{{ $status }}</option>
                @endforeach
            </select>
            <textarea name="notes" class="field" rows="3" placeholder="{{ __('admin.notes') }}"></textarea>
            <button class="btn-primary">{{ __('admin.save') }}</button>
        </form>
        <div class="surface p-5 text-sm">
            <h3 class="font-semibold">History</h3>
            <ul class="mt-3 space-y-2">
                @foreach($order->statusHistory as $history)
                    <li>{{ $history->created_at?->format('Y-m-d H:i') }} — {{ $history->status }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
