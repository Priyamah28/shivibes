<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $items = Wishlist::with('product')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('store.wishlist', compact('items'));
    }

    public function toggle(Request $request, string $slug): RedirectResponse
    {
        $product = Product::where('slug', $slug)->active()->firstOrFail();

        $wishlist = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $message = 'Removed from wishlist.';
        } else {
            Wishlist::create([
                'user_id' => $request->user()->id,
                'product_id' => $product->id,
            ]);
            $message = 'Added to wishlist.';
        }

        return back()->with('success', $message);
    }
}
