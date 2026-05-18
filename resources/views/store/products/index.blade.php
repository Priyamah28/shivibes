@extends('layouts.app')

@section('content')
    <x-store.breadcrumb :items="[['label' => 'Shop']]" />

    <div class="mb-8">
        <h1 class="font-serif text-3xl font-bold text-slate-900">Shop All Products</h1>
        <p class="mt-2 text-slate-600">Herbal skincare, gifting sets and wellness essentials.</p>
    </div>

    <div class="grid gap-8 lg:grid-cols-4">
        <aside class="lg:col-span-1">
            <form method="GET" class="sticky top-28 space-y-6 rounded-2xl border border-brand-100 bg-white p-5">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-brand-700">Search</label>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search products…" class="input-field">
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-brand-700">Category</label>
                    <select name="category" class="input-field">
                        <option value="">All categories</option>
                        @foreach ($filters['categories'] as $cat)
                            <option value="{{ $cat->slug }}" @selected(request('category') === $cat->slug)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-brand-700">Collection</label>
                    <select name="type" class="input-field">
                        <option value="">All types</option>
                        @foreach ($filters['types'] as $key => $label)
                            <option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-xs text-slate-500">Min ₹</label>
                        <input type="number" name="min_price" value="{{ request('min_price') }}" class="input-field">
                    </div>
                    <div>
                        <label class="text-xs text-slate-500">Max ₹</label>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" class="input-field">
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="in_stock" value="1" @checked(request('in_stock')) class="rounded border-brand-300 text-brand-700">
                    In stock only
                </label>

                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-brand-700">Sort</label>
                    <select name="sort" class="input-field">
                        <option value="latest" @selected(request('sort', 'latest') === 'latest')>Newest</option>
                        <option value="bestseller" @selected(request('sort') === 'bestseller')>Bestsellers</option>
                        <option value="price_asc" @selected(request('sort') === 'price_asc')>Price: Low to High</option>
                        <option value="price_desc" @selected(request('sort') === 'price_desc')>Price: High to Low</option>
                        <option value="name" @selected(request('sort') === 'name')>Name A–Z</option>
                    </select>
                </div>

                <button type="submit" class="btn-primary w-full">Apply Filters</button>
                <a href="{{ route('products.index') }}" class="block text-center text-sm text-brand-700 hover:underline">Clear all</a>
            </form>
        </aside>

        <div class="lg:col-span-3">
            <p class="mb-4 text-sm text-slate-500">{{ $products->total() }} products found</p>

            @if ($products->isNotEmpty())
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($products as $product)
                        <x-store.product-card :product="$product" />
                    @endforeach
                </div>
                <div class="mt-8">{{ $products->links() }}</div>
            @else
                <div class="rounded-2xl border border-dashed border-brand-200 bg-white p-16 text-center">
                    <p class="text-lg font-semibold text-slate-800">No products found</p>
                    <p class="mt-2 text-sm text-slate-500">Try adjusting your filters or search term.</p>
                    <a href="{{ route('products.index') }}" class="btn-primary mt-6 inline-flex">View all products</a>
                </div>
            @endif
        </div>
    </div>
@endsection
