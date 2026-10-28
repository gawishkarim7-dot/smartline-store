<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class QuoteRequestAttachment extends Model
{
    protected $fillable = [
        'quote_request_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
    ];

    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }
}
