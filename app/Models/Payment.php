<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'transaction_id',
        'payment_reference',
        'provider',
        'status',
        'amount',
        'currency',
        'payment_method',
        'provider_response',
        'paid_at',
        'refunded_at',
        'notes',
        'receipt_path',
        'receipt_uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'provider_response' => 'array',
            'paid_at' => 'datetime',
            'refunded_at' => 'datetime',
            'receipt_uploaded_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function receiptUrl(): ?string
    {
        return $this->receipt_path ? Storage::disk('public')->url($this->receipt_path) : null;
    }
}
