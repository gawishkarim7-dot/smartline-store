<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    protected $fillable = [
        'order_id',
        'tracking_number',
        'carrier',
        'carrier_service',
        'status',
        'shipping_cost',
        'shipped_at',
        'estimated_delivery_at',
        'delivered_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'shipping_cost' => 'decimal:2',
            'shipped_at' => 'datetime',
            'estimated_delivery_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(ShipmentTrackingEvent::class);
    }
}
