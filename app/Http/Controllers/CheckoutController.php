<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $total = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);

        return view('store.checkout', compact('cart', 'total'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'delivery_type' => 'required|in:normal,express',
        ]);

        $cart = $request->session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('success', 'Cart is empty.');
        }

        $customer = Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
        $shipping = $request->delivery_type === 'express' ? 60 : 0;

        $order = Order::create([
            'order_number' => 'ORD-' . now()->format('YmdHis'),
            'customer_id' => $customer->id,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'razorpay',
            'delivery_type' => $request->delivery_type,
            'subtotal' => $subtotal,
            'discount_amount' => 0,
            'shipping_charge' => $shipping,
            'total_amount' => $subtotal + $shipping,
            'notes' => 'Order placed from website checkout',
        ]);

        foreach ($cart as $slug => $item) {
            $product = Product::where('slug', $slug)->first();
            if (! $product) {
                continue;
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'line_total' => $item['price'] * $item['quantity'],
            ]);
        }

        $request->session()->forget('cart');

        return redirect()->route('home')->with('success', 'Order placed successfully. Razorpay live integration is the next step.');
    }
}
