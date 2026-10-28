<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class QuoteApproval extends Model
{
    protected $fillable = [
        'quote_id',
        'customer_id',
        'approval_token',
        'approved',
        'notes',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'approved' => 'boolean',
            'responded_at' => 'datetime',
        ];
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public static function generateToken(): string
    {
        return Str::random(64);
    }
}
