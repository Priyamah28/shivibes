<section class="{{ $bg ?? 'bg-white' }} py-14">
    <div class="section-container">
        <x-store.section-header :title="$title" :subtitle="$subtitle ?? null" :href="route('products.index')" />
        @if ($products->isNotEmpty())
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($products as $product)
                    <x-store.product-card :product="$product" />
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-brand-200 bg-white p-12 text-center text-slate-500">
                Products coming soon. Run <code class="text-brand-700">php artisan db:seed</code> to populate sample data.
            </div>
        @endif
    </div>
</section>
