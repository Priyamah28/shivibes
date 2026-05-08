@extends('layouts.app')

@section('content')
    <h1 class="mb-6 text-2xl font-bold">Your Cart</h1>

    @if (empty($cart))
        <div class="rounded-xl bg-white p-6 text-sm text-slate-600">Your cart is empty. Add some herbal products first.</div>
    @else
        <div class="space-y-3">
            @foreach ($cart as $slug => $item)
                <div class="flex items-center justify-between rounded-xl border border-emerald-100 bg-white p-4">
                    <div>
                        <h2 class="font-semibold">{{ $item['name'] }}</h2>
                        <p class="text-sm text-slate-600">Qty: {{ $item['quantity'] }} x Rs. {{ number_format($item['price']) }}</p>
                    </div>
                    <form action="{{ route('cart.remove', $slug) }}" method="POST">
                        @csrf
                        <button class="text-sm font-medium text-red-600">Remove</button>
                    </form>
                </div>
            @endforeach
        </div>
        <div class="mt-6 flex items-center justify-between rounded-xl bg-white p-4">
            <span class="font-semibold">Total</span>
            <span class="text-xl font-bold text-emerald-700">Rs. {{ number_format($total) }}</span>
        </div>
        <a href="{{ route('checkout.index') }}" class="mt-4 inline-flex rounded-md bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Proceed to Checkout</a>
    @endif
@endsection
