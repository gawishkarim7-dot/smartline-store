@extends('admin.layouts.app')

@section('title', __('admin.products'))

@section('content')
<div class="mb-4 flex justify-between">
    <a href="{{ route('admin.products.create') }}" class="btn-primary">{{ __('admin.create') }}</a>
</div>
<div class="surface overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-[var(--color-mist)] text-start">
            <tr>
                <th class="px-4 py-3">{{ __('admin.sku') }}</th>
                <th class="px-4 py-3">{{ __('admin.name') }}</th>
                <th class="px-4 py-3">{{ __('admin.type') }}</th>
                <th class="px-4 py-3">{{ __('admin.price') }}</th>
                <th class="px-4 py-3">{{ __('admin.stock') }}</th>
                <th class="px-4 py-3">{{ __('admin.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr class="border-t border-[var(--color-sand-deep)]">
                    <td class="px-4 py-3">{{ $product->sku }}</td>
                    <td class="px-4 py-3">{{ $product->name }}</td>
                    <td class="px-4 py-3">{{ $product->type }}</td>
                    <td class="px-4 py-3">{{ number_format($product->base_price, 2) }}</td>
                    <td class="px-4 py-3">{{ $product->inventory?->quantity_on_hand ?? 0 }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.products.edit', $product) }}" class="font-medium">{{ __('admin.edit') }}</a>
                        <form method="post" action="{{ route('admin.products.destroy', $product) }}" class="inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="ms-2 text-red-700">{{ __('admin.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
