@extends('admin.layouts.app')

@section('title', __('admin.orders'))

@section('content')
<div class="surface overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-[var(--color-mist)]"><tr>
            <th class="px-4 py-3 text-start">#</th>
            <th class="px-4 py-3 text-start">{{ __('admin.customer') }}</th>
            <th class="px-4 py-3 text-start">{{ __('admin.status') }}</th>
            <th class="px-4 py-3 text-start">{{ __('admin.total') }}</th>
            <th class="px-4 py-3 text-start">{{ __('admin.date') }}</th>
        </tr></thead>
        <tbody>
            @foreach($orders as $order)
                <tr class="border-t border-[var(--color-sand-deep)]">
                    <td class="px-4 py-3"><a class="font-medium hover:underline" href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                    <td class="px-4 py-3">{{ $order->customer_full_name }}</td>
                    <td class="px-4 py-3">{{ $order->status }}</td>
                    <td class="px-4 py-3">{{ number_format($order->total, 2) }} {{ $order->currency }}</td>
                    <td class="px-4 py-3">{{ $order->created_at?->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
