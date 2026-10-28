<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_ar',
        'currency_code',
        'phone_code',
        'is_enabled',
        'is_shipping_enabled',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'is_shipping_enabled' => 'boolean',
        ];
    }

    public function localizedName(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $locale === 'ar' && $this->name_ar ? $this->name_ar : $this->name;
    }
}
