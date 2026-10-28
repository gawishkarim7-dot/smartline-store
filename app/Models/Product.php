<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'sku',
        'category_id',
        'type',
        'base_price',
        'sale_price',
        'currency',
        'weight',
        'length',
        'width',
        'height',
        'lead_time_days',
        'is_active',
        'is_featured',
        'published_at',
        'sale_from',
        'sale_until',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'weight' => 'decimal:2',
            'length' => 'decimal:2',
            'width' => 'decimal:2',
            'height' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'sale_from' => 'datetime',
            'sale_until' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function translation(?string $locale = null): ?ProductTranslation
    {
        $locale = $locale ?? app()->getLocale();

        if ($this->relationLoaded('translations')) {
            return $this->translations->firstWhere('locale', $locale)
                ?? $this->translations->firstWhere('locale', config('app.fallback_locale'));
        }

        return $this->translations()->where('locale', $locale)->first()
            ?? $this->translations()->where('locale', config('app.fallback_locale'))->first();
    }

    public function getNameAttribute(): ?string
    {
        return $this->translation()?->name;
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->translation()?->description;
    }

    public function getSpecificationsAttribute(): ?string
    {
        return $this->translation()?->specifications;
    }

    public function currentPrice(): float
    {
        if ($this->sale_price !== null && $this->isOnSale()) {
            return (float) $this->sale_price;
        }

        return (float) $this->base_price;
    }

    public function isOnSale(): bool
    {
        if ($this->sale_price === null) {
            return false;
        }

        $now = now();

        if ($this->sale_from && $now->lt($this->sale_from)) {
            return false;
        }

        if ($this->sale_until && $now->gt($this->sale_until)) {
            return false;
        }

        return true;
    }

    public function primaryImage(): ?ProductImage
    {
        if ($this->relationLoaded('images')) {
            return $this->images->firstWhere('is_primary', true) ?? $this->images->first();
        }

        return $this->images()->where('is_primary', true)->first()
            ?? $this->images()->first();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function allowsDirectPurchase(): bool
    {
        return in_array($this->type, ['ready_made', 'hybrid'], true);
    }

    public function requiresQuote(): bool
    {
        return in_array($this->type, ['custom_made', 'hybrid'], true);
    }
}
