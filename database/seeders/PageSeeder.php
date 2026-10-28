<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'about',
                'sort_order' => 1,
                'en' => [
                    'title' => 'About Smart Line',
                    'content' => '<p>Smart Line crafts metal and wood with precision and passion. From design and fabrication to installation and quality control, we deliver distinctive fit-out and decoration solutions across Saudi Arabia.</p><p>Our factory fabricates cabinets, custom doors, fences, hand rails, louver units, shades, and furniture for hotels, airports, hospitals, and private projects.</p>',
                ],
                'ar' => [
                    'title' => 'عن سمارت لاين',
                    'content' => '<p>تصنع سمارت لاين المعدن والخشب بدقة وشغف. من التصميم والتصنيع إلى التركيب وضبط الجودة، نقدّم حلول تشطيب وديكور مميزة في أنحاء المملكة.</p><p>يصنّع مصنعنا الخزائن والأبواب المخصصة والأسوار والدرابزين ووحدات اللوفر والمظلات والأثاث للفنادق والمطارات والمستشفيات والمشاريع الخاصة.</p>',
                ],
            ],
            [
                'slug' => 'privacy',
                'sort_order' => 2,
                'en' => [
                    'title' => 'Privacy Policy',
                    'content' => '<p>We collect only the information needed to process orders, quote requests, and customer support. Contact and delivery data are protected and used solely for business operations related to Smart Line services.</p>',
                ],
                'ar' => [
                    'title' => 'سياسة الخصوصية',
                    'content' => '<p>نجمع فقط المعلومات اللازمة لمعالجة الطلبات وعروض الأسعار ودعم العملاء. تُحمى بيانات التواصل والتسليم وتُستخدم فقط لعمليات سمارت لاين التجارية.</p>',
                ],
            ],
            [
                'slug' => 'terms',
                'sort_order' => 3,
                'en' => [
                    'title' => 'Terms & Conditions',
                    'content' => '<p>Ready-made products may be purchased online subject to stock and confirmation. Custom products require a formal quotation and written acceptance before production begins. Shipping availability depends on destination and product type.</p>',
                ],
                'ar' => [
                    'title' => 'الشروط والأحكام',
                    'content' => '<p>يمكن شراء المنتجات الجاهزة عبر الإنترنت حسب التوفر والتأكيد. المنتجات المخصصة تتطلب عرض سعر رسمي وقبولاً كتابياً قبل بدء التصنيع. يعتمد الشحن على الوجهة ونوع المنتج.</p>',
                ],
            ],
            [
                'slug' => 'contact',
                'sort_order' => 4,
                'en' => [
                    'title' => 'Contact Us',
                    'content' => '<p>Office: 6857 Al Bahar Al Arabi, Al Yarmuk, Riyadh 13251</p><p>Email: info@smartlinesa.net</p><p>Phone: +966 59 245 8908</p>',
                ],
                'ar' => [
                    'title' => 'تواصل معنا',
                    'content' => '<p>المكتب: 6857 البحر العربي، حي اليرموك، الرياض 13251</p><p>البريد: info@smartlinesa.net</p><p>الهاتف: +966 59 245 8908</p>',
                ],
            ],
        ];

        foreach ($pages as $data) {
            $page = Page::query()->updateOrCreate(
                ['slug' => $data['slug']],
                ['is_active' => true, 'sort_order' => $data['sort_order']]
            );

            foreach (['en', 'ar'] as $locale) {
                $page->translations()->updateOrCreate(
                    ['locale' => $locale],
                    $data[$locale]
                );
            }
        }
    }
}
