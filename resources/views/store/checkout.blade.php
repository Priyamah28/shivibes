@extends('layouts.app')

@section('content')
    <h1 class="mb-6 text-2xl font-bold">Checkout</h1>

    @if (empty($cart))
        <div class="rounded-xl bg-white p-6 text-sm text-slate-600">
            Cart is empty. <a href="{{ route('products.index') }}" class="font-medium text-emerald-700">Browse products</a>.
        </div>
    @else
        <div class="grid gap-6 md:grid-cols-2">
            <form action="{{ route('checkout.store') }}" method="POST" class="rounded-xl bg-white p-5">
                @csrf
                <h2 class="mb-4 font-semibold">Delivery Details</h2>
                <div class="space-y-4">
                    <input type="text" name="name" placeholder="Full name" required class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm">
                    <input type="text" name="phone" placeholder="Phone number" required class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm">
                    <textarea name="address" rows="3" placeholder="Full address" required class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm"></textarea>
                    <select name="delivery_type" required class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm">
                        <option value="normal">Normal Delivery (India Post)</option>
                        <option value="express">Express Delivery (Shiprocket)</option>
                    </select>
                </div>
                <button class="mt-5 rounded-md bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                    Place Order
                </button>
            </form>

            <div class="rounded-xl bg-white p-5">
                <h2 class="mb-4 font-semibold">Order Summary</h2>
                <div class="space-y-2 text-sm">
                    @foreach ($cart as $item)
                        <div class="flex items-center justify-between">
                            <span>{{ $item['name'] }} (x{{ $item['quantity'] }})</span>
                            <span>Rs. {{ number_format($item['price'] * $item['quantity']) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 border-t pt-4">
                    <p class="flex items-center justify-between text-lg font-bold">
                        <span>Total</span>
                        <span class="text-emerald-700">Rs. {{ number_format($total) }}</span>
                    </p>
                    <p class="mt-2 text-xs text-slate-500">Payment gateway: Razorpay integration placeholder for MVP.</p>
                </div>
            </div>
        </div>
    @endif
@endsection
