@props(['product'])

<article {{ $attributes->merge(['class' => 'group card-product']) }}>
    <a href="{{ route('products.show', $product->slug) }}" class="relative block overflow-hidden">
        <img
            src="{{ $product->primaryImage() }}"
            alt="{{ $product->name }}"
            loading="lazy"
            class="h-56 w-full object-cover transition duration-500 group-hover:scale-105"
        >
        @if ($product->isOnSale())
            <span class="badge absolute left-3 top-3 bg-rose-500 text-white">-{{ $product->discountPercent() }}%</span>
        @endif
        @if ($product->is_bestseller)
            <span class="badge absolute right-3 top-3 bg-gold-400 text-white">Bestseller</span>
        @endif
        @if (! $product->inStock())
            <span class="badge absolute bottom-3 left-3 bg-slate-800 text-white">Out of stock</span>
        @endif
    </a>

    <div class="p-5">
        <a href="{{ route('products.show', $product->slug) }}" class="line-clamp-1 text-base font-semibold text-slate-900 hover:text-brand-700">
            {{ $product->name }}
        </a>
        <p class="mt-1 line-clamp-2 text-sm text-slate-600">{{ Str::limit($product->description, 80) }}</p>

        <div class="mt-4 flex items-center justify-between gap-2">
            <div>
                <span class="text-lg font-bold text-brand-800">₹{{ number_format($product->price) }}</span>
                @if ($product->isOnSale())
                    <span class="ml-1 text-sm text-slate-400 line-through">₹{{ number_format($product->compare_at_price) }}</span>
                @endif
            </div>

            <div class="flex items-center gap-1">
                @auth
                    <form action="{{ route('wishlist.toggle', $product->slug) }}" method="POST">
                        @csrf
                        <button type="submit" title="Wishlist" class="rounded-full p-2 text-brand-600 hover:bg-brand-50">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 0 0 0 6.364L12 20.364l7.682-7.682a4.5 4.5 0 0 0-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 0 0-6.364 0z"/></svg>
                        </button>
                    </form>
                    <form action="{{ route('cart.add', $product->slug) }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded-full bg-brand-700 px-4 py-2 text-xs font-semibold text-white hover:bg-brand-800">Add</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-full bg-brand-700 px-4 py-2 text-xs font-semibold text-white hover:bg-brand-800">Shop</a>
                @endauth
            </div>
        </div>
    </div>
</article>
