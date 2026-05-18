<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function cartItems(Request $request): array
    {
        return $request->session()->get('cart', []);
    }

    private function cartTotal(array $cart): float
    {
        return (float) collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
    }

    public function index(Request $request)
    {
        $cart = $this->cartItems($request);
        $total = $this->cartTotal($cart);

        return view('store.cart', compact('cart', 'total'));
    }

    public function summary(Request $request): JsonResponse
    {
        $cart = $this->cartItems($request);

        return response()->json([
            'count' => (int) collect($cart)->sum('quantity'),
            'total' => $this->cartTotal($cart),
            'items' => collect($cart)->map(fn ($item, $slug) => array_merge($item, ['slug' => $slug]))->values(),
        ]);
    }

    public function add(Request $request, string $slug): RedirectResponse
    {
        $product = Product::where('slug', $slug)->active()->first();
        abort_unless($product, 404);

        $cart = $this->cartItems($request);

        if (! isset($cart[$slug])) {
            $cart[$slug] = [
                'name' => $product->name,
                'price' => (float) $product->price,
                'image' => $product->primaryImage(),
                'quantity' => 0,
            ];
        }

        $cart[$slug]['quantity']++;
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Product added to cart.');
    }

    public function remove(Request $request, string $slug): RedirectResponse
    {
        $cart = $this->cartItems($request);
        unset($cart[$slug]);
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Product removed from cart.');
    }
}
