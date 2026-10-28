@extends('store.layouts.app')

@section('title', __('store.cart'))

@section('content')
<section class="section-pad !py-12">
    <div class="section-title-bar">
        <div class="section-kicker">@include('store.partials.icon', ['name' => 'cart']) Smart Line</div>
        <h1 class="font-display mt-2 text-4xl">{{ __('store.cart') }}</h1>
    </div>

    @if($cart->items->isEmpty())
        <div class="surface mt-8 flex flex-col items-start gap-4 p-8">
            <div class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-[rgba(170,103,84,0.12)] text-[var(--color-primary)]">
                @include('store.partials.icon', ['name' => 'cart', 'class' => 'icon icon-lg'])
            </div>
            <p class="text-[var(--color-ink-soft)]">{{ __('store.empty_cart') }}</p>
            <a href="{{ route('store.products.index') }}" class="btn-primary">
                @include('store.partials.icon', ['name' => 'products']) {{ __('store.continue_shopping') }}
            </a>
        </div>
    @else
        <div class="mt-8 overflow-x-auto surface">
            <table class="min-w-full text-sm">
                <thead class="border-b border-[var(--color-sand-deep)] bg-[var(--color-mist)] text-start">
                    <tr>
                        <th class="px-4 py-3 text-start">{{ __('store.products') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('store.price') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('store.quantity') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('store.total') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart->items as $item)
                        <tr class="border-b border-[var(--color-sand-deep)]">
                            <td class="px-4 py-4">
                                <div class="font-medium text-[var(--color-dark)]">{{ $item->product?->name ?? ($item->product_snapshot['name'] ?? '') }}</div>
                                <div class="text-xs text-[var(--color-ink-soft)]">{{ $item->product_snapshot['sku'] ?? '' }}</div>
                            </td>
                            <td class="px-4 py-4">{{ number_format($item->unit_price, 2) }} {{ __('store.sar') }}</td>
                            <td class="px-4 py-4">
                                <form method="post" action="{{ route('store.cart.update', $item) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="0" class="field w-20">
                                    <button class="text-xs font-semibold text-[var(--color-primary)]">{{ __('store.update') }}</button>
                                </form>
                            </td>
                            <td class="px-4 py-4">{{ number_format($item->total_price, 2) }} {{ __('store.sar') }}</td>
                            <td class="px-4 py-4">
                                <form method="post" action="{{ route('store.cart.destroy', $item) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex items-center gap-1 text-xs text-red-700" title="{{ __('store.remove') }}">
                                        @include('store.partials.icon', ['name' => 'trash']) {{ __('store.remove') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
            <a href="{{ route('store.products.index') }}" class="btn-outline">
                @include('store.partials.icon', ['name' => 'products']) {{ __('store.continue_shopping') }}
            </a>
            <div class="text-end">
                <div class="text-sm text-[var(--color-ink-soft)]">{{ __('store.subtotal') }}</div>
                <div class="text-2xl font-semibold text-[var(--color-dark)]">{{ number_format($cart->subtotal, 2) }} {{ __('store.sar') }}</div>
                <a href="{{ route('store.checkout.create') }}" class="btn-primary mt-4">
                    @include('store.partials.icon', ['name' => 'bag']) {{ __('store.checkout') }}
                </a>
            </div>
        </div>
    @endif
</section>
@endsection
