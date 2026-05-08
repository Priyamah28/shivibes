@extends('layouts.app')

@section('content')
    <div class="grid gap-8 rounded-2xl bg-white p-6 md:grid-cols-2">
        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="h-80 w-full rounded-xl object-cover">
        <div>
            <h1 class="text-2xl font-bold">{{ $product->name }}</h1>
            <p class="mt-2 text-sm text-slate-600">{{ $product->description }}</p>

            <p class="mt-4 text-lg font-bold text-emerald-700">Rs. {{ number_format($product->price) }}</p>

            <div class="mt-6 space-y-3 text-sm text-slate-700">
                <p><span class="font-semibold">Ingredients:</span> {{ $product->ingredients }}</p>
                <p><span class="font-semibold">Usage:</span> {{ $product->usage_instructions }}</p>
                <p><span class="font-semibold">Benefits:</span> {{ $product->benefits }}</p>
            </div>

            @auth
                <form action="{{ route('cart.add', $product->slug) }}" method="POST" class="mt-6">
                    @csrf
                    <button class="rounded-md bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                        Add to Cart
                    </button>
                </form>
            @else
                <div class="mt-6 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-700">
                    Please login to add this product to cart.
                    <a href="{{ route('login') }}" class="ml-1 font-semibold underline">Login now</a>
                </div>
            @endauth
        </div>
    </div>
@endsection
