@extends('store.layouts.app')

@section('title', $page->title)

@section('content')
<section class="section-pad !py-12 max-w-3xl">
    <h1 class="font-display text-4xl font-bold">{{ $page->title }}</h1>
    <div class="prose prose-neutral mt-8 max-w-none text-[var(--color-ink-soft)]">
        {!! $page->content !!}
    </div>
</section>
@endsection
