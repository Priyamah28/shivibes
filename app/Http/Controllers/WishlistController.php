<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * @return array{count: int, slugs: list<string>}
     */
    private function wishlistPayload(int $userId): array
    {
        $slugs = Wishlist::query()
            ->where('user_id', $userId)
            ->join('products', 'products.id', '=', 'wishlists.product_id')
            ->pluck('products.slug')
            ->all();

        return [
            'count' => count($slugs),
            'slugs' => $slugs,
        ];
    }

    public function index(Request $request): View
    {
        $items = Wishlist::with('product')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('store.wishlist', compact('items'));
    }

    public function summary(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->wishlistPayload($request->user()->id),
        ]);
    }

    public function toggle(Request $request, string $slug): JsonResponse|RedirectResponse
    {
        $product = Product::where('slug', $slug)->active()->firstOrFail();

        $wishlist = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $message = 'Removed from wishlist.';
            $inWishlist = false;
        } else {
            Wishlist::firstOrCreate([
                'user_id' => $request->user()->id,
                'product_id' => $product->id,
            ]);
            $message = 'Added to wishlist.';
            $inWishlist = true;
        }

        $payload = $this->wishlistPayload($request->user()->id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => array_merge($payload, ['in_wishlist' => $inWishlist]),
            ]);
        }

        return back()->with('success', $message);
    }
}
