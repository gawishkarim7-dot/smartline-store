@extends('admin.layouts.app')

@section('title', __('admin.dashboard'))

@section('content')
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach([
        'total_orders' => $stats['total_orders'],
        'pending_orders' => $stats['pending_orders'],
        'open_quotes' => $stats['open_quotes'],
        'low_stock' => $stats['low_stock'],
    ] as $key => $value)
        <div class="surface p-5">
            <div class="text-sm text-[var(--color-ink-soft)]">{{ __('admin.'.$key) }}</div>
            <div class="mt-2 text-3xl font-semibold">{{ $value }}</div>
        </div>
    @endforeach
</div>

<div class="mt-8 grid gap-6 lg:grid-cols-2">
    <div class="surface p-5">
        <h2 class="font-semibold">{{ __('admin.orders') }}</h2>
        <ul class="mt-4 space-y-3 text-sm">
            @forelse($recentOrders as $order)
                <li class="flex justify-between gap-3">
                    <a class="font-medium hover:underline" href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a>
                    <span>{{ $order->status }}</span>
                </li>
            @empty
                <li class="text-[var(--color-ink-soft)]">—</li>
            @endforelse
        </ul>
    </div>
    <div class="surface p-5">
        <h2 class="font-semibold">{{ __('admin.quote_requests') }}</h2>
        <ul class="mt-4 space-y-3 text-sm">
            @forelse($recentQuotes as $quote)
                <li class="flex justify-between gap-3">
                    <a class="font-medium hover:underline" href="{{ route('admin.quotes.show', $quote) }}">{{ $quote->request_number }}</a>
                    <span>{{ $quote->status }}</span>
                </li>
            @empty
                <li class="text-[var(--color-ink-soft)]">—</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
