@extends('admin.layouts.app')

@section('title', $category->exists ? __('admin.edit') : __('admin.create'))

@section('content')
<form method="post" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="surface max-w-2xl space-y-4 p-6">
    @csrf
    @if($category->exists) @method('PUT') @endif
    <div>
        <label class="label">{{ __('admin.slug') }}</label>
        <input name="slug" value="{{ old('slug', $category->slug) }}" class="field">
    </div>
    <div>
        <label class="label">{{ __('admin.parent') }}</label>
        <select name="parent_id" class="field">
            <option value="">{{ __('admin.none') }}</option>
            @foreach($parents as $parent)
                <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id) == $parent->id)>{{ $parent->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="label">{{ __('admin.name_ar') }}</label>
            <input name="name_ar" value="{{ old('name_ar', $category->translation('ar')?->name) }}" class="field" required>
        </div>
        <div>
            <label class="label">{{ __('admin.name_en') }}</label>
            <input name="name_en" value="{{ old('name_en', $category->translation('en')?->name) }}" class="field" required>
        </div>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="label">{{ __('admin.desc_ar') }}</label>
            <textarea name="description_ar" rows="3" class="field">{{ old('description_ar', $category->translation('ar')?->description) }}</textarea>
        </div>
        <div>
            <label class="label">{{ __('admin.desc_en') }}</label>
            <textarea name="description_en" rows="3" class="field">{{ old('description_en', $category->translation('en')?->description) }}</textarea>
        </div>
    </div>
    <div>
        <label class="label">Sort</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="field">
    </div>
    <label class="text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true))> {{ __('admin.active') }}</label>
    <div class="flex gap-3">
        <button class="btn-primary">{{ __('admin.save') }}</button>
        <a href="{{ route('admin.categories.index') }}" class="btn-secondary">{{ __('admin.back') }}</a>
    </div>
</form>
@endsection
