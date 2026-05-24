<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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

    /**
     * @return array{count: int, total: float, items: \Illuminate\Support\Collection<int, array<string, mixed>>}
     */
    private function cartPayload(Request $request): array
    {
        $cart = $this->cartItems($request);

        return [
            'count' => (int) collect($cart)->sum('quantity'),
            'total' => $this->cartTotal($cart),
            'items' => collect($cart)->map(fn ($item, $slug) => array_merge($item, ['slug' => $slug]))->values(),
        ];
    }

    private function jsonSuccess(Request $request, string $message, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $this->cartPayload($request),
        ], $status);
    }

    public function index(Request $request): View
    {
        $cart = $this->cartItems($request);
        $total = $this->cartTotal($cart);

        return view('store.cart', compact('cart', 'total'));
    }

    public function summary(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->cartPayload($request),
        ]);
    }

    public function add(Request $request, string $slug): JsonResponse|RedirectResponse
    {
        $product = Product::where('slug', $slug)->active()->first();
        abort_unless($product, 404);

        if (! $product->inStock()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This product is out of stock.',
                ], 422);
            }

            return back()->with('error', 'This product is out of stock.');
        }

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

        if ($request->expectsJson()) {
            return $this->jsonSuccess($request, $product->name.' added to cart.');
        }

        return back()->with('success', 'Product added to cart.');
    }

    public function decrement(Request $request, string $slug): JsonResponse|RedirectResponse
    {
        $cart = $this->cartItems($request);

        if (! isset($cart[$slug])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found in cart.',
                ], 404);
            }

            return back()->with('error', 'Item not found in cart.');
        }

        $cart[$slug]['quantity']--;

        if ($cart[$slug]['quantity'] <= 0) {
            unset($cart[$slug]);
        }

        $request->session()->put('cart', $cart);

        if ($request->expectsJson()) {
            return $this->jsonSuccess($request, 'Cart updated.');
        }

        return back()->with('success', 'Cart updated.');
    }

    public function remove(Request $request, string $slug): JsonResponse|RedirectResponse
    {
        $cart = $this->cartItems($request);

        if (! isset($cart[$slug])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found in cart.',
                ], 404);
            }

            return back()->with('error', 'Item not found in cart.');
        }

        unset($cart[$slug]);
        $request->session()->put('cart', $cart);

        if ($request->expectsJson()) {
            return $this->jsonSuccess($request, 'Removed from cart.');
        }

        return back()->with('success', 'Product removed from cart.');
    }
}
