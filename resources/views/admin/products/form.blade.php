@extends('admin.layouts.app')

@section('title', $product->exists ? __('admin.edit') : __('admin.create'))

@section('content')
<form method="post" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" class="surface max-w-3xl space-y-4 p-6">
    @csrf
    @if($product->exists) @method('PUT') @endif

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="label">{{ __('admin.sku') }}</label>
            <input name="sku" value="{{ old('sku', $product->sku) }}" class="field" required>
        </div>
        <div>
            <label class="label">{{ __('admin.categories') }}</label>
            <select name="category_id" class="field" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div>
            <label class="label">{{ __('admin.type') }}</label>
            <select name="type" class="field">
                @foreach(['ready_made','custom_made','hybrid'] as $type)
                    <option value="{{ $type }}" @selected(old('type', $product->type ?? 'ready_made') === $type)>{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">{{ __('admin.price') }}</label>
            <input type="number" step="0.01" name="base_price" value="{{ old('base_price', $product->base_price) }}" class="field" required>
        </div>
        <div>
            <label class="label">Sale</label>
            <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" class="field">
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="label">{{ __('admin.stock') }}</label>
            <input type="number" name="stock" value="{{ old('stock', $product->inventory?->quantity_on_hand ?? 0) }}" class="field">
        </div>
        <div>
            <label class="label">Lead time</label>
            <input type="number" name="lead_time_days" value="{{ old('lead_time_days', $product->lead_time_days ?? 7) }}" class="field">
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="label">{{ __('admin.name_ar') }}</label>
            <input name="name_ar" value="{{ old('name_ar', $product->translation('ar')?->name) }}" class="field" required>
        </div>
        <div>
            <label class="label">{{ __('admin.name_en') }}</label>
            <input name="name_en" value="{{ old('name_en', $product->translation('en')?->name) }}" class="field" required>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="label">{{ __('admin.desc_ar') }}</label>
            <textarea name="description_ar" rows="4" class="field" required>{{ old('description_ar', $product->translation('ar')?->description) }}</textarea>
        </div>
        <div>
            <label class="label">{{ __('admin.desc_en') }}</label>
            <textarea name="description_en" rows="4" class="field" required>{{ old('description_en', $product->translation('en')?->description) }}</textarea>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="label">Specs AR</label>
            <textarea name="specifications_ar" rows="3" class="field">{{ old('specifications_ar', $product->translation('ar')?->specifications) }}</textarea>
        </div>
        <div>
            <label class="label">Specs EN</label>
            <textarea name="specifications_en" rows="3" class="field">{{ old('specifications_en', $product->translation('en')?->specifications) }}</textarea>
        </div>
    </div>

    <div class="flex gap-4 text-sm">
        <label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))> {{ __('admin.active') }}</label>
        <label><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured ?? false))> {{ __('admin.featured') }}</label>
    </div>

    <div class="flex gap-3">
        <button class="btn-primary">{{ __('admin.save') }}</button>
        <a href="{{ route('admin.products.index') }}" class="btn-secondary">{{ __('admin.back') }}</a>
    </div>
</form>
@endsection
