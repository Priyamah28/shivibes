<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Face Care', 'slug' => 'face-care', 'description' => 'Face wash, creams, serums and glow rituals.', 'sort_order' => 1],
            ['name' => 'Hair Care', 'slug' => 'hair-care', 'description' => 'Shampoo, oil and scalp nourishment.', 'sort_order' => 2],
            ['name' => 'Body & Bath', 'slug' => 'body-bath', 'description' => 'Soaps, body lotions and daily care.', 'sort_order' => 3],
            ['name' => 'Spa & Wellness', 'slug' => 'spa-wellness', 'description' => 'Home spa essentials for weekend reset.', 'sort_order' => 4],
            ['name' => 'Corporate Gifts', 'slug' => 'corporate-gifts', 'description' => 'Curated hampers for teams and clients.', 'sort_order' => 5],
            ['name' => 'Festival Hampers', 'slug' => 'festival-hampers', 'description' => 'Diwali, Rakhi and seasonal gifting sets.', 'sort_order' => 6],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                array_merge($category, [
                    'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=600&q=80',
                    'is_active' => true,
                ])
            );
        }
    }
}
