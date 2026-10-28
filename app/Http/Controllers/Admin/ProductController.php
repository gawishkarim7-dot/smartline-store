<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()->with(['translations', 'category.translations', 'inventory'])->latest()->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::query()->with('translations')->orderBy('sort_order')->get();

        return view('admin.products.form', ['product' => new Product, 'categories' => $categories]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            $product = Product::query()->create(collect($data)->except(['name_ar', 'name_en', 'description_ar', 'description_en', 'specifications_ar', 'specifications_en', 'stock'])->all());

            $this->syncTranslations($product, $data);

            Inventory::query()->create([
                'product_id' => $product->id,
                'quantity_on_hand' => $data['stock'] ?? 0,
                'quantity_reserved' => 0,
                'low_stock_threshold' => 5,
            ]);
        });

        return redirect()->route('admin.products.index')->with('success', 'Product created');
    }

    public function edit(Product $product): View
    {
        $product->load(['translations', 'inventory']);
        $categories = Category::query()->with('translations')->orderBy('sort_order')->get();

        return view('admin.products.form', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request, $product);

        DB::transaction(function () use ($product, $data) {
            $product->update(collect($data)->except(['name_ar', 'name_en', 'description_ar', 'description_en', 'specifications_ar', 'specifications_en', 'stock'])->all());
            $this->syncTranslations($product, $data);

            Inventory::query()->updateOrCreate(
                ['product_id' => $product->id, 'product_variant_id' => null],
                ['quantity_on_hand' => $data['stock'] ?? 0]
            );
        });

        return redirect()->route('admin.products.index')->with('success', 'Product updated');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return back()->with('success', 'Product deleted');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku,'.($product?->id ?? 'NULL')],
            'category_id' => ['required', 'exists:categories,id'],
            'type' => ['required', 'in:ready_made,custom_made,hybrid'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'lead_time_days' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'description_ar' => ['required', 'string'],
            'description_en' => ['required', 'string'],
            'specifications_ar' => ['nullable', 'string'],
            'specifications_en' => ['nullable', 'string'],
        ]) + [
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
            'currency' => 'SAR',
            'published_at' => now(),
            'lead_time_days' => $request->integer('lead_time_days') ?: 7,
        ];
    }

    private function syncTranslations(Product $product, array $data): void
    {
        $product->translations()->updateOrCreate(
            ['locale' => 'ar'],
            [
                'name' => $data['name_ar'],
                'description' => $data['description_ar'],
                'specifications' => $data['specifications_ar'] ?? null,
            ]
        );

        $product->translations()->updateOrCreate(
            ['locale' => 'en'],
            [
                'name' => $data['name_en'],
                'description' => $data['description_en'],
                'specifications' => $data['specifications_en'] ?? null,
            ]
        );
    }
}
