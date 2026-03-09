<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Medicine',
                'slug' => 'medicine',
                'description' => 'General medicine and pharmacy items',
            ],
            [
                'name' => 'Healthcare',
                'slug' => 'healthcare',
                'description' => 'Personal and home healthcare essentials',
            ],
            [
                'name' => 'Devices',
                'slug' => 'devices',
                'description' => 'Digital and diagnostic healthcare devices',
            ],
        ];

        foreach ($categories as $category) {
            ProductCategory::updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'image' => 'assets/img/products/default-product.png',
                    'is_active' => true,
                ]
            );
        }

        $products = [
            ['name' => 'Napa Extra 500mg', 'category_slug' => 'medicine', 'price' => 30.00, 'sale_price' => 25.00, 'stock' => 100, 'is_featured' => true],
            ['name' => 'Seclo 20mg Capsule', 'category_slug' => 'medicine', 'price' => 70.00, 'sale_price' => 60.00, 'stock' => 200, 'is_featured' => true],
            ['name' => 'Monas 10mg Tablet', 'category_slug' => 'medicine', 'price' => 160.00, 'sale_price' => 145.00, 'stock' => 150, 'is_featured' => false],
            ['name' => 'Vitamin C 500mg', 'category_slug' => 'medicine', 'price' => 150.00, 'sale_price' => 120.00, 'stock' => 300, 'is_featured' => true],
            ['name' => 'Savlon Antiseptic Liquid', 'category_slug' => 'healthcare', 'price' => 120.00, 'sale_price' => 100.00, 'stock' => 80, 'is_featured' => true],
            ['name' => 'KN95 Face Mask (Pack of 5)', 'category_slug' => 'healthcare', 'price' => 100.00, 'sale_price' => 80.00, 'stock' => 500, 'is_featured' => true],
            ['name' => 'Hand Sanitizer 200ml', 'category_slug' => 'healthcare', 'price' => 180.00, 'sale_price' => null, 'stock' => 100, 'is_featured' => false],
            ['name' => 'Digital Thermometer', 'category_slug' => 'devices', 'price' => 250.00, 'sale_price' => 190.00, 'stock' => 30, 'is_featured' => true],
            ['name' => 'Pulse Oximeter', 'category_slug' => 'devices', 'price' => 1200.00, 'sale_price' => 950.00, 'stock' => 20, 'is_featured' => true],
            ['name' => 'Glucometer Kit', 'category_slug' => 'devices', 'price' => 1500.00, 'sale_price' => 1200.00, 'stock' => 15, 'is_featured' => true],
        ];

        foreach ($products as $item) {
            $category = ProductCategory::where('slug', $item['category_slug'])->first();
            if (!$category) {
                continue;
            }

            $slug = Str::slug($item['name']);

            Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'product_category_id' => $category->id,
                    'brand' => 'Doccure',
                    'tags' => strtolower(str_replace(' ', ',', $item['name'])),
                    'name' => $item['name'],
                    'sku' => strtoupper(substr($item['category_slug'], 0, 3)) . '-' . str_pad((string) $category->id, 2, '0', STR_PAD_LEFT) . '-' . strtoupper(substr(md5($slug), 0, 5)),
                    'description' => $item['name'] . ' for daily healthcare needs.',
                    'meta_title' => $item['name'],
                    'meta_description' => $item['name'] . ' available in Doccure store.',
                    'price' => $item['price'],
                    'sale_price' => $item['sale_price'],
                    'stock' => $item['stock'],
                    'image' => 'assets/img/products/default-product.png',
                    'gallery' => [
                        'assets/img/products/default-product.png',
                    ],
                    'is_active' => true,
                    'is_featured' => $item['is_featured'],
                ]
            );
        }

        $this->command->info('Products seeded with images and categories.');
    }
}
