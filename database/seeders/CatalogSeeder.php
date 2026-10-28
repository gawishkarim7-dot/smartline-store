<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/products_v4.json');
        abort_unless(File::exists($path), 500, 'Missing database/data/products_v4.json');

        $rows = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);

        DB::transaction(function () use ($rows) {
            DB::table('inventories')->delete();
            DB::table('product_variants')->delete();
            DB::table('product_images')->delete();
            DB::table('product_translations')->delete();
            DB::table('products')->delete();
            DB::table('category_translations')->delete();
            DB::table('categories')->delete();

            $sectionMap = [
                'معدني' => ['slug' => 'metal', 'en' => 'Metal Works', 'ar' => 'أعمال معدنية', 'sort' => 1],
                'خشبي' => ['slug' => 'wood', 'en' => 'Wood Works', 'ar' => 'أعمال خشبية', 'sort' => 2],
            ];

            $subMap = [
                'حوامل الكابلات' => 'cable-trays',
                'هاندريل وسلالم' => 'handrails-stairs',
                'أسوار وحماية' => 'fences-gates',
                'ألواح مقصوصة' => 'laser-cut-panels',
                'هياكل وصناديق' => 'frames-enclosures',
                'عزل' => 'insulation',
                'أبواب' => 'doors',
                'مطابخ وخزائن' => 'kitchens-cabinets',
                'أثاث' => 'furniture',
                'ديكور' => 'decor',
                'خامات وألواح' => 'materials-boards',
                'طاقة حيوية' => 'biofuel',
                'أعمال تراثية' => 'heritage-works',
            ];

            $parents = [];
            foreach ($sectionMap as $label => $meta) {
                $parents[$label] = $this->category($meta['slug'], $meta['sort'], [
                    'ar' => ['name' => $meta['ar'], 'description' => 'منتجات وقسم '.$meta['ar']],
                    'en' => ['name' => $meta['en'], 'description' => 'Products under '.$meta['en']],
                ]);
            }

            $subs = [];
            $sort = 10;
            foreach ($rows as $row) {
                $key = $row['section'].'|'.$row['subsection'];
                if (isset($subs[$key])) {
                    continue;
                }

                $slug = $subMap[$row['subsection']] ?? Str::slug($row['subsection']);
                if ($slug === '') {
                    $slug = 'cat-'.substr(md5($row['subsection']), 0, 8);
                }

                $subs[$key] = $this->category($slug, $sort++, [
                    'ar' => ['name' => $row['subsection'], 'description' => 'تصنيف فرعي ضمن '.$row['section']],
                    'en' => [
                        'name' => Str::title(str_replace('-', ' ', $slug)),
                        'description' => 'Subcategory of '.($sectionMap[$row['section']]['en'] ?? $row['section']),
                    ],
                ], $parents[$row['section']]->id ?? null);
            }

            $featured = ['SL-MT-001', 'SL-MT-004', 'SL-MT-007', 'SL-MT-008', 'SL-WD-014', 'SL-WD-019', 'SL-WD-025', 'SL-WD-035'];

            foreach ($rows as $index => $row) {
                $sku = trim((string) $row['sku']);

                $category = $subs[$row['section'].'|'.$row['subsection']] ?? $parents[$row['section']];

                $specsAr = trim(implode("\n", array_filter([
                    $row['options'] ? 'الخيارات: '.$row['options'] : null,
                    $row['material'] && $row['material'] !== '—' ? 'الخامة: '.$row['material'] : null,
                    $row['size_pricing'] ? 'التسعير: '.$row['size_pricing'] : null,
                    $row['sale_type'] ? 'نوع البيع: '.$row['sale_type'] : null,
                ])));

                $specsEn = trim(implode("\n", array_filter([
                    $row['options'] ? 'Options: '.$row['options'] : null,
                    $row['material'] && $row['material'] !== '—' ? 'Material: '.$row['material'] : null,
                    $row['size_pricing'] ? 'Pricing: made to order / quote' : null,
                    $row['sale_type'] ? 'Sale type: custom quote' : null,
                ])));

                $descAr = (string) ($row['desc'] ?? $row['name_ar']);
                $descEn = (string) ($row['desc'] ?? $row['name_en']);

                $product = Product::query()->updateOrCreate(
                    ['sku' => $sku],
                    [
                        'category_id' => $category->id,
                        'type' => 'custom_made',
                        'base_price' => 0,
                        'sale_price' => null,
                        'currency' => 'SAR',
                        'lead_time_days' => 21,
                        'is_active' => true,
                        'is_featured' => in_array($sku, $featured, true) || $index < 3,
                        'published_at' => now(),
                    ]
                );

                $product->translations()->updateOrCreate(
                    ['locale' => 'ar'],
                    [
                        'name' => $row['name_ar'],
                        'description' => $descAr,
                        'specifications' => $specsAr ?: null,
                    ]
                );

                $product->translations()->updateOrCreate(
                    ['locale' => 'en'],
                    [
                        'name' => $row['name_en'],
                        'description' => $descEn,
                        'specifications' => $specsEn ?: null,
                    ]
                );

                Inventory::query()->updateOrCreate(
                    ['product_id' => $product->id, 'product_variant_id' => null],
                    [
                        'quantity_on_hand' => 0,
                        'quantity_reserved' => 0,
                        'low_stock_threshold' => 0,
                    ]
                );
            }
        });
    }

    private function category(string $slug, int $sort, array $translations, ?int $parentId = null): Category
    {
        $category = Category::query()->updateOrCreate(
            ['slug' => $slug],
            [
                'parent_id' => $parentId,
                'is_active' => true,
                'sort_order' => $sort,
            ]
        );

        foreach ($translations as $locale => $data) {
            $category->translations()->updateOrCreate(
                ['locale' => $locale],
                $data
            );
        }

        return $category;
    }
}
