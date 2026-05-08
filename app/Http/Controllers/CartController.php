<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function cartItems(Request $request): array
    {
        return $request->session()->get('cart', []);
    }

    public function index(Request $request)
    {
        $cart = $this->cartItems($request);
        $total = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);

        return view('store.cart', compact('cart', 'total'));
    }

    public function add(Request $request, string $slug): RedirectResponse
    {
        $product = Product::where('slug', $slug)->where('is_active', true)->first();
        abort_unless($product, 404);

        // Keep cart logic very simple for MVP.
        $cart = $request->session()->get('cart', []);

        if (! isset($cart[$slug])) {
            $cart[$slug] = [
                'name' => $product->name,
                'price' => (float) $product->price,
                'quantity' => 0,
            ];
        }

        $cart[$slug]['quantity']++;
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Product added to cart.');
    }

    public function remove(Request $request, string $slug): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$slug]);
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Product removed from cart.');
    }
}
