<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $categories = Category::query()
            ->active()
            ->whereNotNull('parent_id')
            ->with('translations')
            ->orderBy('sort_order')
            ->get();

        $featured = Product::query()
            ->active()
            ->with(['translations', 'images', 'category.translations'])
            ->orderByDesc('is_featured')
            ->orderBy('sku')
            ->take(12)
            ->get();

        $productsCount = Product::query()->active()->count();

        return view('store.home', compact('categories', 'featured', 'productsCount'));
    }
}
