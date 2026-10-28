@extends('store.layouts.app')

@section('title', __('store.quote'))

@section('content')
<section class="section-pad !py-12 max-w-3xl">
    <h1 class="font-display text-4xl font-bold">{{ __('store.quote') }}</h1>
    <p class="mt-2 text-[var(--color-ink-soft)]">{{ __('store.hero_subtitle') }}</p>

    @if($product)
        <div class="surface mt-6 p-4 text-sm">
            {{ __('store.products') }}: <strong>{{ $product->name }}</strong> ({{ $product->sku }})
        </div>
    @endif

    <form method="post" action="{{ route('store.quote.store') }}" enctype="multipart/form-data" class="surface mt-8 space-y-4 p-6">
        @csrf
        @if($product)
            <input type="hidden" name="product_id" value="{{ $product->id }}">
        @endif
        <div>
            <label class="label">{{ __('store.company_name') }}</label>
            <input name="customer_name" value="{{ old('customer_name') }}" class="field" required>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="label">{{ __('store.email') }}</label>
                <input type="email" name="customer_email" value="{{ old('customer_email') }}" class="field" required>
            </div>
            <div>
                <label class="label">{{ __('store.phone') }}</label>
                <input name="customer_phone" value="{{ old('customer_phone') }}" class="field">
            </div>
        </div>
        <div>
            <label class="label">{{ __('store.requirements') }}</label>
            <textarea name="requirements" rows="5" class="field" required>{{ old('requirements') }}</textarea>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="label">{{ __('store.delivery_location') }}</label>
                <input name="delivery_location" value="{{ old('delivery_location') }}" class="field">
            </div>
            <div>
                <label class="label">{{ __('store.estimated_quantity') }}</label>
                <input type="number" name="estimated_quantity" value="{{ old('estimated_quantity') }}" min="1" class="field">
            </div>
        </div>
        <div>
            <label class="label">{{ __('store.attachments') }}</label>
            <input type="file" name="attachments[]" multiple class="field" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx">
        </div>
        <button class="btn-accent">{{ __('store.submit_quote') }}</button>
    </form>
</section>
@endsection
