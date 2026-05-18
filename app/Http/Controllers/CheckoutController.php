<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CheckoutService $checkoutService,
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $user = $request->user();
        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);

        return view('store.checkout', [
            'cart' => $cart,
            'subtotal' => $subtotal,
            'addresses' => $user->addresses()->orderByDesc('is_default')->get(),
            'defaultAddress' => $user->defaultAddress(),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $order = $this->checkoutService->placeOrder(
            $request->user(),
            $request->validated(),
            $cart,
            $request
        );

        return redirect()
            ->route('account.orders.show', $order)
            ->with('success', 'Order placed successfully! Payment integration coming soon.');
    }
}
