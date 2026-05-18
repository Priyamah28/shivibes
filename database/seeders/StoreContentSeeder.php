<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Faq;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class StoreContentSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Ayurvedic Rituals For Everyday Glow',
                'subtitle' => 'Premium herbal skincare inspired by timeless ingredients.',
                'cta_text' => 'Shop New Arrivals',
                'cta_url' => '/products',
                'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=1800&q=80',
                'placement' => 'home_hero',
                'sort_order' => 1,
            ],
            [
                'title' => 'Corporate Gifting Made Effortless',
                'subtitle' => 'Bulk orders, custom branding and GST invoicing for teams.',
                'cta_text' => 'Request Catalogue',
                'cta_url' => '/corporate',
                'image' => 'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?auto=format&fit=crop&w=1800&q=80',
                'placement' => 'home_hero',
                'sort_order' => 2,
            ],
            [
                'title' => 'Festival Collections Are Live',
                'subtitle' => 'Thoughtful herbal hampers for Diwali, Rakhi and more.',
                'cta_text' => 'Explore Hampers',
                'cta_url' => '/products?type=festival',
                'image' => 'https://images.unsplash.com/photo-1607083206869-4c2f2c2c2c2c?auto=format&fit=crop&w=1800&q=80',
                'placement' => 'home_hero',
                'sort_order' => 3,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::updateOrCreate(
                ['title' => $banner['title'], 'placement' => $banner['placement']],
                array_merge($banner, ['is_active' => true])
            );
        }

        $testimonials = [
            ['name' => 'Priya Sharma', 'location' => 'Mumbai', 'rating' => 5, 'body' => 'The face wash and night cream combo transformed my routine. Skin feels calm and visibly brighter within weeks.', 'sort_order' => 1],
            ['name' => 'Rahul Mehta', 'location' => 'Ahmedabad', 'rating' => 5, 'body' => 'We ordered 120 corporate hampers for our annual event. Packaging was premium and delivery was on schedule.', 'sort_order' => 2],
            ['name' => 'Ananya Iyer', 'location' => 'Bengaluru', 'rating' => 5, 'body' => 'Love the herbal fragrance and clean ingredients list. Finally a brand that feels honest and luxurious.', 'sort_order' => 3],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::updateOrCreate(
                ['name' => $testimonial['name']],
                array_merge($testimonial, ['is_active' => true])
            );
        }

        $faqs = [
            ['question' => 'Are Shivibes products 100% herbal?', 'answer' => 'Our formulations are rooted in herbal actives with carefully selected safe ingredients. Full ingredient lists are on every product page.', 'category' => 'products', 'sort_order' => 1],
            ['question' => 'How long does delivery take?', 'answer' => 'Normal delivery via India Post takes 5–8 business days. Express delivery via Shiprocket typically arrives in 2–4 business days.', 'category' => 'shipping', 'sort_order' => 2],
            ['question' => 'Do you offer corporate bulk pricing?', 'answer' => 'Yes. Share your quantity, branding needs and GST details on our corporate page and our team will share a custom quote.', 'category' => 'corporate', 'sort_order' => 3],
            ['question' => 'What is your return policy?', 'answer' => 'Unopened products can be returned within 7 days of delivery. Contact support with your order number to initiate a return.', 'category' => 'returns', 'sort_order' => 4],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(
                ['question' => $faq['question']],
                array_merge($faq, ['is_active' => true])
            );
        }
    }
}
