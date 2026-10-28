<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private CartService $carts) {}

    public function index(): View
    {
        $cart = $this->carts->getOrCreateCart()->load('items.product.translations', 'items.product.images');

        return view('store.cart.index', compact('cart'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $product = Product::query()->active()->findOrFail($data['product_id']);

        abort_unless($product->allowsDirectPurchase(), 422);

        $this->carts->addItem($product, $data['quantity'] ?? 1);

        return back()->with('success', __('store.added_to_cart'));
    }

    public function update(Request $request, CartItem $item): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $this->carts->updateQuantity($item, $data['quantity']);

        return back()->with('success', __('store.update'));
    }

    public function destroy(CartItem $item): RedirectResponse
    {
        $this->carts->removeItem($item);

        return back();
    }
}
