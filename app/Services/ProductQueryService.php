<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductQueryService
{
    public function listing(Request $request)
    {
        $query = Product::active()->with(['categories', 'reviews']);

        if ($request->filled('q')) {
            $term = $request->q;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhereJsonContains('tags', $term);
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('categories', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('type')) {
            $query->ofType($request->type);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            'bestseller' => $query->orderByDesc('is_bestseller')->latest(),
            default => $query->latest(),
        };

        return $query->paginate(12)->withQueryString();
    }

    public function filters(): array
    {
        return [
            'categories' => Category::active()->whereNull('parent_id')->orderBy('sort_order')->get(),
            'types' => [
                'personal' => 'Personal Gifting',
                'corporate' => 'Corporate Gifting',
                'festival' => 'Festival Collections',
                'combo' => 'Combo Packs',
            ],
            'budgets' => [
                ['label' => 'Under ₹500', 'min' => 0, 'max' => 500],
                ['label' => '₹500 – ₹999', 'min' => 500, 'max' => 999],
                ['label' => '₹1000 – ₹1999', 'min' => 1000, 'max' => 1999],
                ['label' => '₹2000+', 'min' => 2000, 'max' => null],
            ],
            'occasions' => ['Diwali', 'Rakhi', 'Wedding', 'Birthday', 'Anniversary', 'Housewarming'],
            'recipients' => ['For Her', 'For Him', 'For Parents', 'For Colleagues', 'For Clients'],
        ];
    }
}
