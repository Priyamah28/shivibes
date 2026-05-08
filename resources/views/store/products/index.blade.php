@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">All Products</h1>
        <p class="text-sm text-slate-600">Herbal essentials for daily skincare.</p>
    </div>

    <div class="grid gap-5 md:grid-cols-3">
        @foreach ($products as $product)
            <article class="overflow-hidden rounded-2xl border border-emerald-100 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="h-48 w-full object-cover">
                <div class="p-5">
                    <h2 class="font-semibold text-lg">{{ $product->name }}</h2>
                    <p class="mt-1 text-sm text-slate-600">{{ $product->description }}</p>
                    <div class="mt-5 flex items-center justify-between">
                        <span class="font-bold text-emerald-700">Rs. {{ number_format($product->price) }}</span>
                        <a href="{{ route('products.show', $product->slug) }}" class="rounded-md bg-emerald-50 px-3 py-1.5 text-sm font-medium text-emerald-700">View</a>
                    </div>

                    <div class="mt-3">
                        @auth
                            <form action="{{ route('cart.add', $product->slug) }}" method="POST">
                                @csrf
                                <button class="inline-flex items-center gap-2 rounded-md border border-emerald-200 px-3 py-1.5 text-sm font-medium text-emerald-700 hover:bg-emerald-50">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.4 10.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.6L21 7H7.2" />
                                    </svg>
                                    Add to cart
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-md border border-amber-200 bg-amber-50 px-3 py-1.5 text-sm font-medium text-amber-700">
                                Login to add
                            </a>
                        @endauth
                    </div>
                </div>
            </article>
        @endforeach
    </div>
@endsection
