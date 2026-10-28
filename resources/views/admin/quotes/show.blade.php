@extends('admin.layouts.app')

@section('title', $quoteRequest->request_number)

@section('content')
<div class="grid gap-6 lg:grid-cols-2">
    <div class="space-y-6">
        <div class="surface p-5 text-sm">
            <div><strong>{{ __('admin.customer') }}:</strong> {{ $quoteRequest->customer_name }} · {{ $quoteRequest->customer_email }}</div>
            <div class="mt-2"><strong>{{ __('admin.status') }}:</strong> {{ $quoteRequest->status }}</div>
            <div class="mt-4"><strong>{{ __('store.requirements') }}</strong><p class="mt-1 text-[var(--color-ink-soft)]">{{ $quoteRequest->requirements }}</p></div>
            @if($quoteRequest->attachments->isNotEmpty())
                <div class="mt-4">
                    <strong>{{ __('store.attachments') }}</strong>
                    <ul class="mt-2 list-disc ps-5">
                        @foreach($quoteRequest->attachments as $attachment)
                            <li><a class="hover:underline" href="{{ $attachment->url() }}" target="_blank">{{ $attachment->file_name }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <form method="post" action="{{ route('admin.quotes.status', $quoteRequest) }}" class="surface space-y-3 p-5">
            @csrf
            @method('PUT')
            <label class="label">{{ __('admin.update_status') }}</label>
            <select name="status" class="field">
                @foreach(['draft','under_review','technical_review','commercial_review','sent','accepted','rejected','expired','cancelled'] as $status)
                    <option value="{{ $status }}" @selected($quoteRequest->status === $status)>{{ $status }}</option>
                @endforeach
            </select>
            <textarea name="internal_notes" class="field" rows="3">{{ old('internal_notes', $quoteRequest->internal_notes) }}</textarea>
            <button class="btn-primary">{{ __('admin.save') }}</button>
        </form>
    </div>

    <div class="space-y-6">
        <form method="post" action="{{ route('admin.quotes.store-quote', $quoteRequest) }}" class="surface space-y-3 p-5">
            @csrf
            <h3 class="font-semibold">{{ __('admin.create_quote') }}</h3>
            <input name="description" class="field" placeholder="Description" required>
            <div class="grid grid-cols-2 gap-3">
                <input type="number" name="quantity" value="1" min="1" class="field" required>
                <input type="number" step="0.01" name="unit_price" class="field" placeholder="{{ __('admin.unit_price') }}" required>
            </div>
            <input type="number" step="0.01" name="shipping_estimate" class="field" placeholder="Shipping estimate">
            <textarea name="notes" class="field" rows="2" placeholder="{{ __('admin.notes') }}"></textarea>
            <label class="text-sm"><input type="checkbox" name="send" value="1"> {{ __('admin.send_quote') }}</label>
            <button class="btn-accent">{{ __('admin.create_quote') }}</button>
        </form>

        <div class="surface p-5 text-sm">
            <h3 class="font-semibold">{{ __('admin.quotes') }}</h3>
            <ul class="mt-3 space-y-3">
                @forelse($quoteRequest->quotes as $quote)
                    <li class="border-b border-[var(--color-sand-deep)] pb-3">
                        <div class="font-medium">{{ $quote->quote_number }} — {{ $quote->status }}</div>
                        <div>{{ number_format($quote->total, 2) }} {{ $quote->currency }}</div>
                    </li>
                @empty
                    <li>—</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
