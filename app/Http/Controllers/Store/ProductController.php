<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::query()
            ->active()
            ->with(['translations', 'images', 'category.translations', 'inventory']);

        if ($search = $request->string('q')->toString()) {
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categorySlug = $request->string('category')->toString()) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        if ($type = $request->string('type')->toString()) {
            if (in_array($type, ['ready_made', 'custom_made', 'hybrid'], true)) {
                $query->where('type', $type);
            }
        }

        $sort = $request->string('sort')->toString();
        match ($sort) {
            'price_low' => $query->orderBy('base_price'),
            'price_high' => $query->orderByDesc('base_price'),
            default => $query->latest(),
        };

        $products = $query->paginate(36)->withQueryString();
        $categories = Category::query()->active()->with('translations')->orderBy('sort_order')->get();
        $productsCount = Product::query()->active()->count();

        return view('store.products.index', compact('products', 'categories', 'productsCount'));
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load(['translations', 'images', 'variants', 'inventory', 'category.translations']);

        $related = Product::query()
            ->active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['translations', 'images'])
            ->take(4)
            ->get();

        return view('store.products.show', compact('product', 'related'));
    }
}
