<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()->with(['translations', 'parent.translations'])->orderBy('sort_order')->paginate(30);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $parents = Category::query()->with('translations')->orderBy('sort_order')->get();

        return view('admin.categories.form', ['category' => new Category, 'parents' => $parents]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            $category = Category::query()->create([
                'parent_id' => $data['parent_id'] ?: null,
                'slug' => $data['slug'],
                'is_active' => $data['is_active'],
                'sort_order' => $data['sort_order'],
            ]);
            $this->syncTranslations($category, $data);
        });

        return redirect()->route('admin.categories.index')->with('success', 'Category created');
    }

    public function edit(Category $category): View
    {
        $category->load('translations');
        $parents = Category::query()->where('id', '!=', $category->id)->with('translations')->orderBy('sort_order')->get();

        return view('admin.categories.form', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validated($request, $category);

        DB::transaction(function () use ($category, $data) {
            $category->update([
                'parent_id' => $data['parent_id'] ?: null,
                'slug' => $data['slug'],
                'is_active' => $data['is_active'],
                'sort_order' => $data['sort_order'],
            ]);
            $this->syncTranslations($category, $data);
        });

        return redirect()->route('admin.categories.index')->with('success', 'Category updated');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return back()->with('success', 'Category deleted');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        $slug = $request->string('slug')->toString() ?: Str::slug($request->string('name_en')->toString());

        return $request->validate([
            'parent_id' => ['nullable', 'exists:categories,id'],
            'slug' => ['nullable', 'string', 'max:150', 'unique:categories,slug,'.($category?->id ?? 'NULL')],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
        ]) + [
            'slug' => $slug,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->integer('sort_order') ?: 0,
        ];
    }

    private function syncTranslations(Category $category, array $data): void
    {
        $category->translations()->updateOrCreate(
            ['locale' => 'ar'],
            ['name' => $data['name_ar'], 'description' => $data['description_ar'] ?? null]
        );
        $category->translations()->updateOrCreate(
            ['locale' => 'en'],
            ['name' => $data['name_en'], 'description' => $data['description_en'] ?? null]
        );
    }
}
