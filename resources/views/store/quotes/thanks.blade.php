@extends('store.layouts.app')

@section('title', __('store.quote_submitted'))

@section('content')
<section class="section-pad !py-16 max-w-3xl text-center text-center">
    <h1 class="font-display text-4xl font-bold">{{ __('store.quote_submitted') }}</h1>
    <p class="mt-4 text-lg">{{ __('store.request_number') }}: <strong>{{ $quoteRequest->request_number }}</strong></p>
    <p class="mt-2 text-[var(--color-ink-soft)]">{{ config('store.contact.email') }} · {{ config('store.contact.phone') }}</p>
    <a href="{{ route('store.home') }}" class="btn-primary mt-8">{{ __('store.home') }}</a>
</section>
@endsection
