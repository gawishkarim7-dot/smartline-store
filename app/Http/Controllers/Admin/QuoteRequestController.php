<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteApproval;
use App\Models\QuoteItem;
use App\Models\QuoteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuoteRequestController extends Controller
{
    public function index(): View
    {
        $quoteRequests = QuoteRequest::query()->with('latestQuote')->latest()->paginate(20);

        return view('admin.quotes.index', compact('quoteRequests'));
    }

    public function show(QuoteRequest $quoteRequest): View
    {
        $quoteRequest->load(['attachments', 'quotes.items']);

        return view('admin.quotes.show', compact('quoteRequest'));
    }

    public function updateStatus(Request $request, QuoteRequest $quoteRequest): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:draft,under_review,technical_review,commercial_review,sent,accepted,rejected,expired,cancelled'],
            'internal_notes' => ['nullable', 'string'],
        ]);

        $quoteRequest->update($data);

        return back()->with('success', 'Updated');
    }

    public function storeQuote(Request $request, QuoteRequest $quoteRequest): RedirectResponse
    {
        $data = $request->validate([
            'description' => ['required', 'string', 'max:1000'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'shipping_estimate' => ['nullable', 'numeric', 'min:0'],
            'validity_days' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
            'send' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($quoteRequest, $data) {
            $lineTotal = $data['quantity'] * $data['unit_price'];
            $tax = $data['tax_amount'] ?? round($lineTotal * 0.15, 2);
            $shipping = $data['shipping_estimate'] ?? 0;
            $total = $lineTotal + $tax + $shipping;
            $send = ! empty($data['send']);

            $quote = Quote::query()->create([
                'quote_number' => Quote::generateQuoteNumber(),
                'quote_request_id' => $quoteRequest->id,
                'status' => $send ? 'sent' : 'draft',
                'currency' => 'SAR',
                'subtotal' => $lineTotal,
                'tax_amount' => $tax,
                'shipping_estimate' => $shipping,
                'total' => $total,
                'validity_days' => $data['validity_days'] ?? 30,
                'notes' => $data['notes'] ?? null,
                'sent_at' => $send ? now() : null,
                'expires_at' => now()->addDays($data['validity_days'] ?? 30),
            ]);

            QuoteItem::query()->create([
                'quote_id' => $quote->id,
                'description' => $data['description'],
                'quantity' => $data['quantity'],
                'unit_price' => $data['unit_price'],
                'total_price' => $lineTotal,
            ]);

            QuoteApproval::query()->create([
                'quote_id' => $quote->id,
                'approval_token' => QuoteApproval::generateToken(),
            ]);

            if ($send) {
                $quoteRequest->update(['status' => 'sent']);
            }
        });

        return back()->with('success', 'Quote created');
    }
}
