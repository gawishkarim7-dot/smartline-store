@extends('admin.layouts.app')

@section('title', __('admin.categories'))

@section('content')
<div class="mb-4"><a href="{{ route('admin.categories.create') }}" class="btn-primary">{{ __('admin.create') }}</a></div>
<div class="surface overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-[var(--color-mist)]"><tr>
            <th class="px-4 py-3 text-start">{{ __('admin.slug') }}</th>
            <th class="px-4 py-3 text-start">{{ __('admin.name') }}</th>
            <th class="px-4 py-3 text-start">{{ __('admin.parent') }}</th>
            <th class="px-4 py-3 text-start">{{ __('admin.actions') }}</th>
        </tr></thead>
        <tbody>
            @foreach($categories as $category)
                <tr class="border-t border-[var(--color-sand-deep)]">
                    <td class="px-4 py-3">{{ $category->slug }}</td>
                    <td class="px-4 py-3">{{ $category->name }}</td>
                    <td class="px-4 py-3">{{ $category->parent?->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.categories.edit', $category) }}">{{ __('admin.edit') }}</a>
                        <form method="post" action="{{ route('admin.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="ms-2 text-red-700">{{ __('admin.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $categories->links() }}</div>
@endsection
