@extends('layouts.app')

@php
    $title = ($product->meta_title ?? $product->name) . ' | Shivibes';
    $metaDescription = $product->meta_description ?? Str::limit($product->description, 160);
    $images = $product->images->pluck('image_path')->prepend($product->primaryImage())->unique()->values();
@endphp

@section('content')
    <x-store.breadcrumb :items="[
        ['label' => 'Shop', 'url' => route('products.index')],
        ['label' => $product->name],
    ]" />

    <div class="grid gap-10 lg:grid-cols-2" x-data="{ activeImage: 0, images: @js($images) }">
        {{-- Gallery --}}
        <div>
            <div class="overflow-hidden rounded-2xl border border-brand-100 bg-white">
                <img :src="images[activeImage]" alt="{{ $product->name }}" class="h-[420px] w-full object-cover transition duration-300">
            </div>
            @if ($images->count() > 1)
                <div class="mt-3 flex gap-2 overflow-x-auto">
                    <template x-for="(img, i) in images" :key="i">
                        <button @click="activeImage = i" :class="activeImage === i ? 'ring-2 ring-brand-600' : ''" class="shrink-0 overflow-hidden rounded-lg">
                            <img :src="img" class="h-16 w-16 object-cover" :alt="'{{ $product->name }} view ' + (i+1)">
                        </button>
                    </template>
                </div>
            @endif
        </div>

        {{-- Details --}}
        <div>
            @if ($product->is_bestseller)
                <span class="badge bg-gold-100 text-gold-800">Bestseller</span>
            @endif
            <h1 class="mt-2 font-serif text-3xl font-bold text-slate-900">{{ $product->name }}</h1>

            @if ($product->reviews->isNotEmpty())
                <p class="mt-2 text-sm text-slate-600">★ {{ $product->averageRating() }} ({{ $product->reviews->count() }} reviews)</p>
            @endif

            <div class="mt-4 flex items-baseline gap-3">
                <span class="text-2xl font-bold text-brand-800">₹{{ number_format($product->price) }}</span>
                @if ($product->isOnSale())
                    <span class="text-lg text-slate-400 line-through">₹{{ number_format($product->compare_at_price) }}</span>
                    <span class="badge bg-rose-100 text-rose-700">Save {{ $product->discountPercent() }}%</span>
                @endif
            </div>

            <p class="mt-4 text-slate-600 leading-relaxed">{{ $product->description }}</p>

            @if ($product->variants->isNotEmpty())
                <div class="mt-6">
                    <p class="text-sm font-semibold text-slate-800">Select variant</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($product->variants as $variant)
                            <span class="rounded-full border border-brand-200 px-4 py-2 text-sm">{{ $variant->name }} — ₹{{ number_format($variant->price) }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <p class="mt-4 text-sm {{ $product->inStock() ? 'text-brand-700' : 'text-rose-600' }}">
                {{ $product->inStock() ? 'In stock — ships in 2–5 business days' : 'Currently out of stock' }}
            </p>

            @if ($product->moq > 1)
                <p class="mt-1 text-xs text-slate-500">Minimum order quantity: {{ $product->moq }} units</p>
            @endif

            <div class="mt-6 flex flex-wrap gap-3">
                @auth
                    @if ($product->inStock())
                        <form action="{{ route('cart.add', $product->slug) }}" method="POST">
                            @csrf
                            <button class="btn-primary">Add to Cart</button>
                        </form>
                    @endif
                    <form action="{{ route('wishlist.toggle', $product->slug) }}" method="POST">
                        @csrf
                        <button class="btn-secondary">♡ Wishlist</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-primary">Login to Purchase</a>
                @endauth

                <a href="https://wa.me/919000000000?text={{ urlencode('Hi, I am interested in ' . $product->name) }}" target="_blank" rel="noopener" class="btn-secondary">WhatsApp</a>

                <button
                    onclick="navigator.share?.({ title: '{{ $product->name }}', url: window.location.href }) ?? navigator.clipboard.writeText(window.location.href)"
                    class="btn-ghost"
                >Share</button>
            </div>

            <div class="mt-8 space-y-4 rounded-2xl border border-brand-100 bg-brand-50/50 p-5 text-sm">
                @if ($product->ingredients)
                    <div><span class="font-semibold">Ingredients:</span> {{ $product->ingredients }}</div>
                @endif
                @if ($product->usage_instructions)
                    <div><span class="font-semibold">How to use:</span> {{ $product->usage_instructions }}</div>
                @endif
                @if ($product->benefits)
                    <div><span class="font-semibold">Benefits:</span> {{ $product->benefits }}</div>
                @endif
            </div>
        </div>
    </div>

    @if ($product->reviews->isNotEmpty())
        <section class="mt-16">
            <h2 class="section-title">Customer Reviews</h2>
            <div class="mt-6 grid gap-4 md:grid-cols-2">
                @foreach ($product->reviews->take(4) as $review)
                    <article class="rounded-xl border border-brand-100 bg-white p-5">
                        <p class="text-gold-500 text-sm">{{ str_repeat('★', $review->rating) }}</p>
                        <p class="mt-2 text-sm text-slate-600">{{ $review->body }}</p>
                        <p class="mt-2 text-xs font-semibold text-slate-800">— {{ $review->reviewer_name }}</p>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    @if ($related->isNotEmpty())
        <section class="mt-16">
            <x-store.section-header title="You May Also Like" :href="route('products.index')" />
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($related as $item)
                    <x-store.product-card :product="$item" />
                @endforeach
            </div>
        </section>
    @endif

    @if ($recentlyViewed->isNotEmpty())
        <section class="mt-16">
            <x-store.section-header title="Recently Viewed" />
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($recentlyViewed as $item)
                    <x-store.product-card :product="$item" />
                @endforeach
            </div>
        </section>
    @endif
@endsection
