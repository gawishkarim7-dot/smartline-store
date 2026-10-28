<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\Setting;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(private CartService $carts) {}

    public function create(): View|RedirectResponse
    {
        $cart = $this->carts->getOrCreateCart()->load('items.product.translations');

        if ($cart->items->isEmpty()) {
            return redirect()->route('store.cart.index');
        }

        $countries = Country::query()->where('is_enabled', true)->orderBy('name')->get();
        $bank = config('store.bank');

        return view('store.checkout.create', compact('cart', 'countries', 'bank'));
    }

    public function store(Request $request): RedirectResponse
    {
        $cart = $this->carts->getOrCreateCart()->load('items.product');

        if ($cart->items->isEmpty()) {
            return redirect()->route('store.cart.index');
        }

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'country_code' => ['required', 'string', 'size:2'],
            'customer_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $taxRate = ((int) Setting::getValue('tax_rate', 15)) / 100;
        $subtotal = (float) $cart->subtotal;
        $taxAmount = round($subtotal * $taxRate, 2);
        $shippingAmount = 0;
        $total = $subtotal + $taxAmount + $shippingAmount;

        $order = DB::transaction(function () use ($cart, $data, $subtotal, $taxAmount, $shippingAmount, $total) {
            $order = Order::query()->create([
                'order_number' => Order::generateOrderNumber(),
                'customer_email' => $data['email'],
                'customer_first_name' => $data['first_name'],
                'customer_last_name' => $data['last_name'],
                'customer_phone' => $data['phone'] ?? null,
                'locale' => app()->getLocale(),
                'status' => 'pending_payment',
                'currency' => $cart->currency,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'discount_amount' => 0,
                'total' => $total,
                'customer_notes' => $data['customer_notes'] ?? null,
            ]);

            foreach ($cart->items as $item) {
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->product_snapshot['name'] ?? $item->product?->name ?? 'Product',
                    'product_sku' => $item->product_snapshot['sku'] ?? $item->product?->sku ?? '',
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                    'product_snapshot' => $item->product_snapshot,
                ]);
            }

            OrderAddress::query()->create([
                'order_id' => $order->id,
                'type' => 'shipping',
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'address_line_1' => $data['address_line_1'],
                'address_line_2' => $data['address_line_2'] ?? null,
                'city' => $data['city'],
                'state' => $data['state'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'country_code' => $data['country_code'],
                'phone' => $data['phone'] ?? null,
            ]);

            OrderStatusHistory::query()->create([
                'order_id' => $order->id,
                'status' => 'pending_payment',
                'notes' => 'Order created — awaiting bank transfer to Smart Line IBAN',
            ]);

            Payment::query()->create([
                'order_id' => $order->id,
                'payment_reference' => $order->order_number,
                'status' => 'pending',
                'amount' => $total,
                'currency' => $cart->currency,
                'payment_method' => 'bank_transfer',
                'provider' => 'riyadh_bank',
                'notes' => 'Transfer must be sent to Smart Line Company for Industry IBAN SA9020000002073345729940 (Riyadh Bank) using order number as reference.',

            ]);

            $this->carts->clear($cart);

            return $order;
        });

        try {
            Mail::raw(
                "New Smart Line order {$order->order_number}\nCustomer: {$order->customer_full_name}\nEmail: {$order->customer_email}\nTotal: {$order->total} {$order->currency}\nPayment: bank transfer to company IBAN — pending verification.",
                function ($message) use ($order) {
                    $message->to(config('store.contact.email'))
                        ->subject("New order {$order->order_number} — payment pending");
                }
            );
        } catch (\Throwable $e) {
            Log::warning('Order notification email failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
        }

        return redirect()->route('store.checkout.thanks', $order);
    }

    public function thanks(Order $order): View
    {
        $order->load('payments');
        $instructions = Setting::getValue(
            app()->getLocale() === 'ar' ? 'bank_transfer_instructions_ar' : 'bank_transfer_instructions_en',
            ''
        );
        $bank = config('store.bank');
        $payment = $order->payments->first();

        return view('store.checkout.thanks', compact('order', 'instructions', 'bank', 'payment'));
    }

    public function uploadReceipt(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'receipt' => ['required', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf'],
        ]);

        $payment = $order->payments()->latest()->firstOrFail();

        $path = $data['receipt']->store('payment-receipts/'.$order->id, 'public');

        $payment->update([
            'receipt_path' => $path,
            'receipt_uploaded_at' => now(),
            'status' => 'processing',
            'notes' => trim(($payment->notes ? $payment->notes."\n" : '').'Customer uploaded transfer receipt.'),
        ]);

        $order->update(['status' => 'payment_verification']);

        OrderStatusHistory::query()->create([
            'order_id' => $order->id,
            'status' => 'payment_verification',
            'notes' => 'Customer uploaded bank transfer receipt — awaiting admin confirmation that funds appear in company account.',
        ]);

        try {
            Mail::raw(
                "Receipt uploaded for order {$order->order_number}. Please verify the transfer landed in Smart Line Riyadh Bank account (IBAN SA9020000002073345729940).",

                function ($message) use ($order) {
                    $message->to(config('store.contact.email'))
                        ->subject("Receipt uploaded — {$order->order_number}");
                }
            );
        } catch (\Throwable $e) {
            Log::warning('Receipt notification email failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
        }

        return back()->with('success', __('store.receipt_uploaded'));
    }
}
