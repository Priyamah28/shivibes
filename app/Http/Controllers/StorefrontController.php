<?php

namespace App\Http\Controllers;

use App\Models\Product;

class StorefrontController extends Controller
{
    public function home()
    {
        $products = Product::where('is_active', true)->latest()->get();

        return view('store.home', [
            'newArrivals' => $products->take(8),
            'bestSellers' => $products->sortByDesc('stock')->take(8)->values(),
            'featuredProducts' => $products->take(4),
        ]);
    }

    public function products()
    {
        return view('store.products.index', [
            'products' => Product::where('is_active', true)->latest()->get(),
        ]);
    }

    public function showProduct(string $slug)
    {
        $product = Product::where('slug', $slug)->where('is_active', true)->first();

        abort_unless($product, 404);

        return view('store.products.show', ['product' => $product]);
    }
}
