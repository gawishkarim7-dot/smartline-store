<?php

return [
    'default_currency' => env('STORE_DEFAULT_CURRENCY', 'SAR'),
    'base_currency' => env('STORE_BASE_CURRENCY', 'SAR'),
    'default_country' => env('STORE_DEFAULT_COUNTRY', 'SA'),
    'payment_gateway_enabled' => (bool) env('PAYMENT_GATEWAY_ENABLED', false),
    'shipping_enabled' => (bool) env('SHIPPING_ENABLED', true),
    'company_website' => env('COMPANY_WEBSITE_URL', 'https://smartlinesa.net'),
    'contact' => [
        'email' => 'info@smartlinesa.net',
        'phone' => '+966 59 245 8908',
        'address' => '6857 Al Bahar Al Arabi, Al Yarmuk, Riyadh 13251',
        'address_ar' => '6857 البحر العربي، حي اليرموك، الرياض 13251',
    ],
    'bank' => [
        'name_ar' => 'بنك الرياض',
        'name_en' => 'Riyadh Bank',
        'account_name_ar' => 'شركة الخط الذكي للصناعة',
        'account_name_en' => 'SMART LINE COMPANY FOR INDUSTRY',
        'account_number' => '2073345729940',
        'iban' => 'SA9020000002073345729940',
        'swift' => 'RIBLSARI',
        'currency' => 'SAR',
        'branch_ar' => 'فرع السليمانية',
        'branch_en' => 'Al Sulaymaniah Branch',
    ],
];


