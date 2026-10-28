<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()->with('payments')->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['items', 'addresses', 'payments', 'statusHistory.changedBy']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending_payment,payment_verification,confirmed,processing,manufacturing,ready,shipped,delivered,cancelled,completed'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $order->update(['status' => $data['status']]);

        OrderStatusHistory::query()->create([
            'order_id' => $order->id,
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
            'changed_by' => auth()->id(),
        ]);

        return back()->with('success', 'Status updated');
    }

    public function confirmPayment(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($order, $data) {
            $payment = $order->payments()->latest()->first();

            if ($payment) {
                $payment->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                    'notes' => trim(($payment->notes ? $payment->notes."\n" : '').($data['notes'] ?? 'Admin confirmed funds received in Smart Line bank account.')),
                ]);
            }

            $order->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            OrderStatusHistory::query()->create([
                'order_id' => $order->id,
                'status' => 'confirmed',
                'notes' => $data['notes'] ?? 'Payment confirmed — amount verified in company bank account (Riyadh Bank).',

                'changed_by' => auth()->id(),
            ]);
        });

        return back()->with('success', __('admin.payment_confirmed'));
    }
}
