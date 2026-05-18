<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Neem Face Wash',
                'slug' => 'neem-face-wash',
                'description' => 'Deep cleansing face wash for oily and acne-prone skin.',
                'ingredients' => 'Neem, Aloe Vera, Tulsi',
                'usage_instructions' => 'Use twice daily on damp face and rinse with water.',
                'benefits' => 'Cleans pores, controls excess oil, refreshes skin.',
                'price' => 249,
                'compare_at_price' => 299,
                'stock' => 38,
                'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=80',
                'product_type' => 'personal',
                'is_featured' => true,
                'is_trending' => true,
                'category' => 'face-care',
            ],
            [
                'name' => 'Saffron Glow Cream',
                'slug' => 'saffron-glow-cream',
                'description' => 'Daily herbal cream for soft and radiant skin.',
                'ingredients' => 'Saffron, Licorice, Almond Oil',
                'usage_instructions' => 'Apply a small amount on clean face after cleansing.',
                'benefits' => 'Hydrates skin, improves glow, smoothens texture.',
                'price' => 399,
                'compare_at_price' => 449,
                'stock' => 22,
                'image' => 'https://images.unsplash.com/photo-1611930022073-b7a4ba5fcccd?auto=format&fit=crop&w=900&q=80',
                'product_type' => 'personal',
                'is_bestseller' => true,
                'is_featured' => true,
                'category' => 'face-care',
            ],
            [
                'name' => 'Kumkumadi Night Oil',
                'slug' => 'kumkumadi-night-oil',
                'description' => 'Traditional overnight facial oil for skin repair.',
                'ingredients' => 'Kumkumadi Oil, Rose, Vetiver',
                'usage_instructions' => 'Massage 2-3 drops on face before bedtime.',
                'benefits' => 'Nourishes deeply, supports skin renewal, adds glow.',
                'price' => 549,
                'stock' => 12,
                'image' => 'https://images.unsplash.com/photo-1570194065650-d99fb4f9f0f4?auto=format&fit=crop&w=900&q=80',
                'product_type' => 'personal',
                'is_bestseller' => true,
                'category' => 'face-care',
            ],
            [
                'name' => 'Herbal Hair Revive Oil',
                'slug' => 'herbal-hair-revive-oil',
                'description' => 'Strengthening hair oil with bhringraj and amla.',
                'ingredients' => 'Bhringraj, Amla, Coconut Oil',
                'usage_instructions' => 'Massage into scalp twice a week, leave for 30 minutes.',
                'benefits' => 'Reduces breakage, nourishes roots, adds shine.',
                'price' => 329,
                'stock' => 45,
                'image' => 'https://images.unsplash.com/photo-1608248543779-f6bb4f4e0c0a?auto=format&fit=crop&w=900&q=80',
                'product_type' => 'personal',
                'is_trending' => true,
                'category' => 'hair-care',
            ],
            [
                'name' => 'Diwali Wellness Hamper',
                'slug' => 'diwali-wellness-hamper',
                'description' => 'Curated festival hamper with face care and body essentials.',
                'ingredients' => 'Assorted herbal products',
                'usage_instructions' => 'Gift-ready set for festive occasions.',
                'benefits' => 'Premium gifting experience with natural skincare.',
                'price' => 1999,
                'stock' => 15,
                'image' => 'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?auto=format&fit=crop&w=900&q=80',
                'product_type' => 'festival',
                'is_featured' => true,
                'moq' => 5,
                'category' => 'festival-hampers',
            ],
            [
                'name' => 'Corporate Wellness Box',
                'slug' => 'corporate-wellness-box',
                'description' => 'Bulk-ready corporate gift box with branding options.',
                'ingredients' => 'Assorted herbal products',
                'usage_instructions' => 'Ideal for employee and client gifting programs.',
                'benefits' => 'Custom branding, GST invoice, volume pricing.',
                'price' => 1499,
                'stock' => 50,
                'image' => 'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?auto=format&fit=crop&w=900&q=80',
                'product_type' => 'corporate',
                'moq' => 25,
                'category' => 'corporate-gifts',
            ],
            [
                'name' => 'Glow Ritual Combo Pack',
                'slug' => 'glow-ritual-combo-pack',
                'description' => 'Face wash + cream + night oil combo at special value.',
                'ingredients' => 'Neem, Saffron, Kumkumadi',
                'usage_instructions' => 'Follow AM/PM routine as listed on each product.',
                'benefits' => 'Complete daily ritual in one value pack.',
                'price' => 999,
                'compare_at_price' => 1197,
                'stock' => 20,
                'image' => 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=900&q=80',
                'product_type' => 'combo',
                'is_bestseller' => true,
                'is_trending' => true,
                'category' => 'face-care',
            ],
        ];

        foreach ($products as $data) {
            $categorySlug = $data['category'];
            unset($data['category']);

            $product = Product::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, [
                    'sku' => strtoupper(Str::slug($data['slug'], '_')),
                    'is_active' => true,
                    'tags' => ['herbal', 'natural', 'skincare'],
                ])
            );

            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $product->categories()->syncWithoutDetaching([$category->id]);
            }
        }
    }
}
