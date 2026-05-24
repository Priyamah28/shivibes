@extends('admin.layout')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Products',
        'subtitle' => 'Manage catalog by gifting type, category, and homepage placement.',
        'actionUrl' => route('admin.products.create'),
        'actionLabel' => '+ Add Product',
    ])

    <form method="GET" class="mb-6 flex flex-wrap items-end gap-3 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
        <div class="min-w-[140px] flex-1">
            <label class="text-xs font-medium text-slate-500">Search</label>
            <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name or SKU" class="input-field">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500">Gifting type</label>
            <select name="type" class="input-field">
                <option value="">All types</option>
                @foreach ($types as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['type'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500">Category</label>
            <select name="category" class="input-field">
                <option value="">All categories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->slug }}" @selected(($filters['category'] ?? '') === $cat->slug)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary py-2.5 mt-5">Filter</button>
        <a href="{{ route('admin.products.index') }}" class="btn-ghost">Reset</a>
    </form>

    <div class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <!-- <table class="min-w-full text-left text-sm"> -->
            <table class="min-w-full table-fixed text-left text-sm">
                <thead class="bg-brand-50 text-xs uppercase tracking-wider text-brand-800">
                    <tr>
                        <!-- <th class="px-4 py-3">Product</th> -->
                        <th class="w-[320px] px-4 py-3">Product</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Categories</th>
                        <th class="px-4 py-3">Price</th>
                        <th class="px-4 py-3">Stock</th>
                        <th class="px-4 py-3">Flags</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-50">
                    @forelse ($products as $product)
                        <tr class="hover:bg-brand-50/50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3 min-w-[260px]">
                                    <img src="{{ str_starts_with($product->image ?? '', 'http') ? $product->image : asset(ltrim($product->image ?? '', '/')) }}" alt="" class="h-14 w-14 min-h-[56px] min-w-[56px] rounded-lg object-cover border border-brand-100 bg-white flex-shrink-0" onerror="this.src='{{ \App\Models\Product::defaultImageUrl() }}'">
                                    <div class="min-w-0">
                                        <!-- <p class="font-medium text-slate-900">{{ $product->name }}</p> -->
                                        <p class="line-clamp-2 text-sm font-medium text-slate-900">{{ $product->name }}</p>

                                        <!-- <p class="text-xs text-slate-500">{{ $product->sku }}</p> -->
                                        <p class="truncate text-xs text-slate-500">{{ $product->sku }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-brand-100 text-brand-800">{{ $types[$product->product_type] ?? $product->product_type }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">
                                {{ $product->categories->pluck('name')->join(', ') ?: '—' }}
                            </td>
                            <td class="px-4 py-3 font-medium">₹{{ number_format($product->price) }}</td>
                            <td class="px-4 py-3 {{ $product->stock <= 5 ? 'font-semibold text-amber-700' : '' }}">{{ $product->stock }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @if ($product->is_featured)<span class="badge bg-gold-100 text-gold-800">Featured</span>@endif
                                    @if ($product->is_bestseller)<span class="badge bg-brand-100 text-brand-800">Best</span>@endif
                                    @if ($product->is_trending)<span class="badge bg-rose-100 text-rose-800">Hot</span>@endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $product->is_active ? 'Active' : 'Hidden' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-brand-700 hover:underline">Edit</a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="mt-1 inline" onsubmit="return confirm('Delete this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-rose-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-slate-500">No products found. <a href="{{ route('admin.products.create') }}" class="text-brand-700 underline">Add your first product</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($products->hasPages())
            <div class="border-t border-brand-100 px-4 py-3">{{ $products->links() }}</div>
        @endif
    </div>
@endsection
