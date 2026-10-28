<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\QuoteRequest;
use App\Models\QuoteRequestAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuoteRequestController extends Controller
{
    public function create(Request $request): View
    {
        $product = null;
        if ($request->filled('product')) {
            $product = Product::query()->active()->with('translations')->find($request->integer('product'));
        }

        return view('store.quotes.create', compact('product'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'requirements' => ['required', 'string', 'max:5000'],
            'delivery_location' => ['nullable', 'string', 'max:500'],
            'estimated_quantity' => ['nullable', 'integer', 'min:1'],
            'product_id' => ['nullable', 'exists:products,id'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx'],
        ]);

        $quoteRequest = DB::transaction(function () use ($data, $request) {
            $specifications = [];
            if (! empty($data['product_id'])) {
                $product = Product::query()->with('translations')->find($data['product_id']);
                if ($product) {
                    $specifications['product_id'] = $product->id;
                    $specifications['product_sku'] = $product->sku;
                    $specifications['product_name'] = $product->name;
                }
            }

            $quoteRequest = QuoteRequest::query()->create([
                'request_number' => QuoteRequest::generateRequestNumber(),
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'locale' => app()->getLocale(),
                'status' => 'under_review',
                'requirements' => $data['requirements'],
                'specifications' => $specifications ?: null,
                'delivery_location' => $data['delivery_location'] ?? null,
                'estimated_quantity' => $data['estimated_quantity'] ?? null,
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('quote-attachments/'.$quoteRequest->id, 'public');
                    QuoteRequestAttachment::query()->create([
                        'quote_request_id' => $quoteRequest->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientMimeType() ?? 'application/octet-stream',
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            return $quoteRequest;
        });

        return redirect()->route('store.quote.thanks', $quoteRequest);
    }

    public function thanks(QuoteRequest $quoteRequest): View
    {
        return view('store.quotes.thanks', compact('quoteRequest'));
    }
}
