<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::insert([
            [
                'name' => 'Neem Face Wash',
                'slug' => 'neem-face-wash',
                'description' => 'Deep cleansing face wash for oily and acne-prone skin.',
                'ingredients' => 'Neem, Aloe Vera, Tulsi',
                'usage_instructions' => 'Use twice daily on damp face and rinse with water.',
                'benefits' => 'Cleans pores, controls excess oil, refreshes skin.',
                'price' => 249,
                'stock' => 38,
                'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=80',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Saffron Glow Cream',
                'slug' => 'saffron-glow-cream',
                'description' => 'Daily herbal cream for soft and radiant skin.',
                'ingredients' => 'Saffron, Licorice, Almond Oil',
                'usage_instructions' => 'Apply a small amount on clean face after cleansing.',
                'benefits' => 'Hydrates skin, improves glow, smoothens texture.',
                'price' => 399,
                'stock' => 22,
                'image' => 'https://images.unsplash.com/photo-1611930022073-b7a4ba5fcccd?auto=format&fit=crop&w=900&q=80',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
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
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
