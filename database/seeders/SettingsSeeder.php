<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Currency;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'store_name', 'value' => 'Smart Line', 'type' => 'string', 'group' => 'general'],
            ['key' => 'store_tagline', 'value' => 'Metal and Wood', 'type' => 'string', 'group' => 'general'],
            ['key' => 'tax_rate', 'value' => '15', 'type' => 'integer', 'group' => 'commerce'],
            [
                'key' => 'bank_transfer_instructions_ar',
                'value' => "التحويل يجب أن يصل مباشرة إلى حساب شركة الخط الذكي للصناعة فقط.\nضع رقم الطلب في البيان/المرجع، ثم ارفع إيصال التحويل من صفحة الطلب.\nلا يُعتمد الطلب إلا بعد ظهور المبلغ في حساب الشركة.\n\nالبنك: بنك الرياض\nالفرع: السليمانية\nاسم الحساب: شركة الخط الذكي للصناعة\nرقم الحساب: 2073345729940\nIBAN: SA9020000002073345729940\nSWIFT: RIBLSARI\nالعملة: ريال سعودي (SAR)",
                'type' => 'string',
                'group' => 'payment',
            ],
            [
                'key' => 'bank_transfer_instructions_en',
                'value' => "Transfer must go directly to Smart Line Company for Industry only.\nUse the order number as the transfer reference, then upload the receipt on the order page.\nThe order is confirmed only after funds appear in the company account.\n\nBank: Riyadh Bank\nBranch: Al Sulaymaniah\nAccount name: SMART LINE COMPANY FOR INDUSTRY\nAccount number: 2073345729940\nIBAN: SA9020000002073345729940\nSWIFT: RIBLSARI\nCurrency: SAR",
                'type' => 'string',
                'group' => 'payment',
            ],
            ['key' => 'bank_name_ar', 'value' => 'بنك الرياض', 'type' => 'string', 'group' => 'payment'],
            ['key' => 'bank_name_en', 'value' => 'Riyadh Bank', 'type' => 'string', 'group' => 'payment'],
            ['key' => 'bank_account_name_ar', 'value' => 'شركة الخط الذكي للصناعة', 'type' => 'string', 'group' => 'payment'],
            ['key' => 'bank_account_name_en', 'value' => 'SMART LINE COMPANY FOR INDUSTRY', 'type' => 'string', 'group' => 'payment'],
            ['key' => 'bank_account_number', 'value' => '2073345729940', 'type' => 'string', 'group' => 'payment'],
            ['key' => 'bank_iban', 'value' => 'SA9020000002073345729940', 'type' => 'string', 'group' => 'payment'],
            ['key' => 'bank_swift', 'value' => 'RIBLSARI', 'type' => 'string', 'group' => 'payment'],
        ];



        foreach ($settings as $setting) {
            Setting::query()->updateOrCreate(['key' => $setting['key']], $setting);
        }

        Currency::query()->updateOrCreate(
            ['code' => 'SAR'],
            ['name' => 'Saudi Riyal', 'symbol' => 'ر.س', 'exchange_rate' => 1, 'is_enabled' => true]
        );

        Currency::query()->updateOrCreate(
            ['code' => 'USD'],
            ['name' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 0.27, 'is_enabled' => true]
        );

        $countries = [
            ['code' => 'SA', 'name' => 'Saudi Arabia', 'name_ar' => 'المملكة العربية السعودية', 'currency_code' => 'SAR', 'phone_code' => '+966', 'is_enabled' => true, 'is_shipping_enabled' => true],
            ['code' => 'AE', 'name' => 'United Arab Emirates', 'name_ar' => 'الإمارات العربية المتحدة', 'currency_code' => 'AED', 'phone_code' => '+971', 'is_enabled' => true, 'is_shipping_enabled' => true],
            ['code' => 'KW', 'name' => 'Kuwait', 'name_ar' => 'الكويت', 'currency_code' => 'KWD', 'phone_code' => '+965', 'is_enabled' => true, 'is_shipping_enabled' => false],
            ['code' => 'BH', 'name' => 'Bahrain', 'name_ar' => 'البحرين', 'currency_code' => 'BHD', 'phone_code' => '+973', 'is_enabled' => true, 'is_shipping_enabled' => false],
            ['code' => 'QA', 'name' => 'Qatar', 'name_ar' => 'قطر', 'currency_code' => 'QAR', 'phone_code' => '+974', 'is_enabled' => true, 'is_shipping_enabled' => false],
            ['code' => 'OM', 'name' => 'Oman', 'name_ar' => 'عُمان', 'currency_code' => 'OMR', 'phone_code' => '+968', 'is_enabled' => true, 'is_shipping_enabled' => false],
        ];

        foreach ($countries as $country) {
            Country::query()->updateOrCreate(['code' => $country['code']], $country);
        }
    }
}
