@props([
    'product',
    'compact' => false,
])

@php
    $inWishlist = auth()->check() && in_array($product->slug, $wishlistSlugs ?? [], true);
@endphp

<div
    x-data="productActions(@js($product->slug), @js($inWishlist))"
    {{ $attributes->merge(['class' => 'flex items-center gap-1']) }}
>
    @auth
        <button
            type="button"
            @click="toggleWishlist()"
            :disabled="wishlistLoading"
            :class="{ 'is-active': inWishlist, 'is-loading': wishlistLoading }"
            class="btn-icon wishlist-heart"
            title="Wishlist"
            aria-label="Toggle wishlist"
        >
            <svg class="h-5 w-5 transition" :class="inWishlist ? 'fill-rose-500 text-rose-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 0 0 0 6.364L12 20.364l7.682-7.682a4.5 4.5 0 0 0-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 0 0-6.364 0z"/>
            </svg>
        </button>

        @if ($product->inStock())
            <div x-show="$store.nav.cartQty(slug) < 1" class="flex">
                <button
                    type="button"
                    @click="addToCart()"
                    :disabled="cartLoading"
                    :class="{ 'is-loading': cartLoading }"
                    class="{{ $compact ? 'rounded-full bg-brand-700 px-4 py-2 text-xs font-semibold text-white hover:bg-brand-800 disabled:opacity-60' : 'btn-primary' }}"
                >
                    <span x-show="!cartLoading">{{ $compact ? 'Add' : 'Add to Cart' }}</span>
                    <span x-show="cartLoading" x-cloak class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span class="sr-only">Adding…</span>
                    </span>
                </button>
            </div>

            <div
                x-show="$store.nav.cartQty(slug) > 0"
                x-cloak
                class="qty-stepper {{ $compact ? 'qty-stepper--compact' : '' }}"
            >
                <button
                    type="button"
                    @click="decrementCart()"
                    :disabled="cartLoading"
                    class="qty-stepper__btn"
                    aria-label="Decrease quantity"
                >−</button>
                <span class="qty-stepper__value" x-text="$store.nav.cartQty(slug)"></span>
                <button
                    type="button"
                    @click="addToCart()"
                    :disabled="cartLoading"
                    class="qty-stepper__btn"
                    aria-label="Increase quantity"
                >+</button>
            </div>
        @endif
    @else
        <a href="{{ route('login') }}" class="{{ $compact ? 'rounded-full bg-brand-700 px-4 py-2 text-xs font-semibold text-white' : 'btn-primary' }}">
            {{ $compact ? 'Shop' : 'Login to Purchase' }}
        </a>
    @endauth
</div>
