<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class QuoteRequest extends Model
{
    protected $fillable = [
        'request_number',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'locale',
        'status',
        'requirements',
        'specifications',
        'delivery_location',
        'estimated_quantity',
        'internal_notes',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'specifications' => 'array',
            'expires_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(QuoteRequestAttachment::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function latestQuote(): HasOne
    {
        return $this->hasOne(Quote::class)->latestOfMany();
    }

    public static function generateRequestNumber(): string
    {
        do {
            $number = 'QR-'.now()->format('Ymd').'-'.strtoupper(substr(uniqid(), -5));
        } while (self::where('request_number', $number)->exists());

        return $number;
    }
}
