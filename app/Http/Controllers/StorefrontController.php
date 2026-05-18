<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\HomePageService;
use App\Services\ProductQueryService;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function __construct(
        private HomePageService $homePage,
        private ProductQueryService $productQuery,
    ) {}

    public function home()
    {
        return view('store.home', $this->homePage->data());
    }

    public function products(Request $request)
    {
        return view('store.products.index', [
            'products' => $this->productQuery->listing($request),
            'filters' => $this->productQuery->filters(),
        ]);
    }

    public function showProduct(Request $request, string $slug)
    {
        
        $product = Product::with(['categories', 'images', 'variants', 'reviews'])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        $this->trackRecentlyViewed($request, $product->id);

        $related = Product::active()
            ->where('id', '!=', $product->id)
            ->when($product->categories->isNotEmpty(), function ($q) use ($product) {
                $q->whereHas('categories', fn ($c) => $c->whereIn('categories.id', $product->categories->pluck('id')));
            })
            ->take(4)
            ->get();

        $recentlyViewed = $this->recentlyViewedProducts($request, $product->id);

        return view('store.products.show', compact('product', 'related', 'recentlyViewed'));
    }

    private function trackRecentlyViewed(Request $request, int $productId): void
    {
        $viewed = collect($request->session()->get('recently_viewed', []))
            ->prepend($productId)
            ->unique()
            ->take(8)
            ->values()
            ->all();

        $request->session()->put('recently_viewed', $viewed);
    }

    private function recentlyViewedProducts(Request $request, int $excludeId)
    {
        $ids = collect($request->session()->get('recently_viewed', []))
            ->reject(fn ($id) => $id === $excludeId)
            ->take(4);

        if ($ids->isEmpty()) {
            return collect();
        }

        return Product::active()->whereIn('id', $ids)->get();
    }
}
