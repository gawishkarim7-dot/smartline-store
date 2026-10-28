<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\QuoteRequest;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'total_orders' => Order::query()->count(),
            'pending_orders' => Order::query()->where('status', 'pending_payment')->count(),
            'open_quotes' => QuoteRequest::query()->whereIn('status', ['under_review', 'technical_review', 'commercial_review', 'draft'])->count(),
            'low_stock' => Inventory::query()->get()->filter->isLowStock()->count(),
        ];

        $recentOrders = Order::query()->latest()->take(5)->get();
        $recentQuotes = QuoteRequest::query()->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'recentQuotes'));
    }
}
