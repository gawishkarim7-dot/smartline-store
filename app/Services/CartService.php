<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class CartService
{
    public function getOrCreateCart(): Cart
    {
        $token = request()->cookie('cart_token') ?? session('cart_token');

        if ($token) {
            $cart = Cart::query()->where('guest_token', $token)->with('items.product.translations', 'items.product.images')->first();
            if ($cart) {
                return $cart;
            }
        }

        $token = (string) Str::uuid();
        session(['cart_token' => $token]);
        Cookie::queue('cart_token', $token, 60 * 24 * 14);

        return Cart::query()->create([
            'guest_token' => $token,
            'currency' => config('store.default_currency', 'SAR'),
            'expires_at' => now()->addDays(14),
        ]);
    }

    public function addItem(Product $product, int $quantity = 1, ?int $variantId = null): CartItem
    {
        $cart = $this->getOrCreateCart();
        $unitPrice = $product->currentPrice();

        $existing = $cart->items()
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variantId)
            ->first();

        if ($existing) {
            $existing->update([
                'quantity' => $existing->quantity + $quantity,
                'unit_price' => $unitPrice,
                'total_price' => ($existing->quantity + $quantity) * $unitPrice,
            ]);
            $cart->recalculate();

            return $existing->fresh();
        }

        $item = $cart->items()->create([
            'product_id' => $product->id,
            'product_variant_id' => $variantId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $quantity * $unitPrice,
            'product_snapshot' => [
                'name' => $product->name,
                'sku' => $product->sku,
                'type' => $product->type,
                'price' => $unitPrice,
            ],
        ]);

        $cart->recalculate();

        return $item;
    }

    public function updateQuantity(CartItem $item, int $quantity): void
    {
        if ($quantity <= 0) {
            $cart = $item->cart;
            $item->delete();
            $cart->recalculate();

            return;
        }

        $item->update([
            'quantity' => $quantity,
            'total_price' => $quantity * $item->unit_price,
        ]);

        $item->cart->recalculate();
    }

    public function removeItem(CartItem $item): void
    {
        $cart = $item->cart;
        $item->delete();
        $cart->recalculate();
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
        $cart->recalculate();
    }

    public function itemCount(): int
    {
        $token = request()->cookie('cart_token') ?? session('cart_token');

        if (! $token) {
            return 0;
        }

        $cartId = Cart::query()->where('guest_token', $token)->value('id');

        if (! $cartId) {
            return 0;
        }

        return (int) CartItem::query()->where('cart_id', $cartId)->sum('quantity');
    }
}
