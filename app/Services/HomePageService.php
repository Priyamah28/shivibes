<?php

namespace App\Services;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Product;
use App\Models\Testimonial;

class HomePageService
{
    public function data(): array
    {
        return [
            'banners' => Banner::active()->where('placement', 'home_hero')->get(),
            'categories' => Category::active()->whereNull('parent_id')->with('children')->orderBy('sort_order')->get(),
            'newArrivals' => Product::active()->latest()->take(8)->get(),
            'bestSellers' => Product::active()->where('is_bestseller', true)->take(8)->get(),
            'trending' => Product::active()->where('is_trending', true)->take(8)->get(),
            'featured' => Product::active()->where('is_featured', true)->take(4)->get(),
            'corporateProducts' => Product::active()->ofType('corporate')->take(4)->get(),
            'festivalProducts' => Product::active()->ofType('festival')->take(4)->get(),
            'comboProducts' => Product::active()->ofType('combo')->take(4)->get(),
            'personalProducts' => Product::active()->ofType('personal')->take(4)->get(),
            'testimonials' => Testimonial::active()->take(6)->get(),
            'faqs' => Faq::active()->take(6)->get(),
        ];
    }
}
