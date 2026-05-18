<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function suggest(Request $request)
    {
        $term = trim((string) $request->get('q', ''));

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $products = Product::active()
            ->where('name', 'like', "%{$term}%")
            ->select('name', 'slug', 'price', 'image')
            ->limit(6)
            ->get()
            ->map(fn ($p) => [
                'name' => $p->name,
                'slug' => $p->slug,
                'price' => (float) $p->price,
                'image' => $p->primaryImage(),
                'url' => route('products.show', $p->slug),
            ]);

        return response()->json($products);
    }
}
