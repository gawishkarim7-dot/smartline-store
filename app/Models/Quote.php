<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quote extends Model
{
    protected $fillable = [
        'quote_number',
        'quote_request_id',
        'status',
        'currency',
        'subtotal',
        'tax_amount',
        'shipping_estimate',
        'total',
        'validity_days',
        'terms_and_conditions',
        'notes',
        'sent_at',
        'accepted_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'shipping_estimate' => 'decimal:2',
            'total' => 'decimal:2',
            'sent_at' => 'datetime',
            'accepted_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function approval(): HasOne
    {
        return $this->hasOne(QuoteApproval::class);
    }

    public static function generateQuoteNumber(): string
    {
        do {
            $number = 'QT-'.now()->format('Ymd').'-'.strtoupper(substr(uniqid(), -5));
        } while (self::where('quote_number', $number)->exists());

        return $number;
    }
}
