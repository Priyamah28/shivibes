@props(['product'])

<article {{ $attributes->merge(['class' => 'group/card card-product']) }}>
    <a href="{{ route('products.show', $product->slug) }}" class="relative block overflow-hidden">
        <div class="relative aspect-[4/5] overflow-hidden bg-brand-100 sm:h-56 sm:aspect-auto">
            <img
                src="{{ $product->primaryImage() }}"
                alt="{{ $product->name }}"
                loading="lazy"
                class="h-full w-full object-cover transition duration-700 ease-out group-hover/card:scale-110"
                onerror="this.onerror=null;this.src='{{ \App\Models\Product::defaultImageUrl() }}'"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-brand-950/50 via-transparent to-transparent opacity-80 transition group-hover/card:opacity-90"></div>
        </div>
        @if ($product->isOnSale())
            <span class="badge absolute left-3 top-3 bg-rose-500 text-white shadow-md">-{{ $product->discountPercent() }}%</span>
        @endif
        @if ($product->is_bestseller)
            <span class="badge absolute right-3 top-3 bg-gradient-to-r from-gold-500 to-gold-400 text-white shadow-md">Bestseller</span>
        @endif
        @if (! $product->inStock())
            <span class="badge absolute bottom-3 left-3 bg-slate-900/90 text-white backdrop-blur-sm">Out of stock</span>
        @endif
        <span class="absolute bottom-3 right-3 rounded-full bg-white/95 px-3 py-1.5 text-xs font-semibold text-brand-800 opacity-0 shadow-md backdrop-blur-sm transition duration-300 group-hover/card:opacity-100">
            View product →
        </span>
    </a>

    <div class="p-5">
        @if ($product->relationLoaded('reviews') && $product->reviews->isNotEmpty())
            <p class="mb-1 text-xs font-medium text-gold-600">
                <span class="text-gold-500">★</span> {{ number_format($product->averageRating(), 1) }}
                <span class="text-slate-400">({{ $product->reviews->count() }})</span>
            </p>
        @endif
        <a href="{{ route('products.show', $product->slug) }}" class="line-clamp-1 font-serif text-lg font-semibold text-slate-900 transition group-hover/card:text-brand-800">
            {{ $product->name }}
        </a>
        <p class="mt-1.5 line-clamp-2 text-sm leading-relaxed text-slate-600">{{ Str::limit($product->description, 80) }}</p>

        <div class="mt-5 flex items-end justify-between gap-2 border-t border-brand-50 pt-4">
            <div>
                <span class="text-xl font-bold text-brand-900">₹{{ number_format($product->price) }}</span>
                @if ($product->isOnSale())
                    <span class="ml-1.5 text-sm text-slate-400 line-through">₹{{ number_format($product->compare_at_price) }}</span>
                @endif
            </div>

            <x-store.product-actions :product="$product" compact />
        </div>
    </div>
</article>
