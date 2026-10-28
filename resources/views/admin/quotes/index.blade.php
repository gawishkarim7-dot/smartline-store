@extends('admin.layouts.app')

@section('title', __('admin.quote_requests'))

@section('content')
<div class="surface overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-[var(--color-mist)]"><tr>
            <th class="px-4 py-3 text-start">#</th>
            <th class="px-4 py-3 text-start">{{ __('admin.customer') }}</th>
            <th class="px-4 py-3 text-start">{{ __('admin.status') }}</th>
            <th class="px-4 py-3 text-start">{{ __('admin.date') }}</th>
        </tr></thead>
        <tbody>
            @foreach($quoteRequests as $quoteRequest)
                <tr class="border-t border-[var(--color-sand-deep)]">
                    <td class="px-4 py-3"><a class="font-medium hover:underline" href="{{ route('admin.quotes.show', $quoteRequest) }}">{{ $quoteRequest->request_number }}</a></td>
                    <td class="px-4 py-3">{{ $quoteRequest->customer_name }}</td>
                    <td class="px-4 py-3">{{ $quoteRequest->status }}</td>
                    <td class="px-4 py-3">{{ $quoteRequest->created_at?->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $quoteRequests->links() }}</div>
@endsection
