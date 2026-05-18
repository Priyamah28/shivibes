@php
    $product = $product ?? null;
    $selectedCategories = $selectedCategories ?? [];
    $tagsString = old('tags', $product ? implode(', ', $product->tags ?? []) : '');
@endphp

<div class="grid gap-8 lg:grid-cols-2">
    <div class="space-y-6 lg:col-span-2">
        <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
            <h2 class="mb-4 font-semibold text-brand-800">Basic information</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium">Product name *</label>
                    <input type="text" name="name" value="{{ old('name', $product?->name) }}" required class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $product?->slug) }}" placeholder="auto-generated" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $product?->sku) }}" class="input-field">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium">Description *</label>
                    <textarea name="description" rows="4" required class="input-field">{{ old('description', $product?->description) }}</textarea>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
            <h2 class="mb-4 font-semibold text-brand-800">Product details</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium">Ingredients</label>
                    <textarea name="ingredients" rows="2" class="input-field">{{ old('ingredients', $product?->ingredients) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium">Usage instructions</label>
                    <textarea name="usage_instructions" rows="2" class="input-field">{{ old('usage_instructions', $product?->usage_instructions) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium">Benefits</label>
                    <textarea name="benefits" rows="2" class="input-field">{{ old('benefits', $product?->benefits) }}</textarea>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
            <h2 class="mb-4 font-semibold text-brand-800">SEO</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium">Meta title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $product?->meta_title) }}" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium">Meta description</label>
                    <textarea name="meta_description" rows="2" class="input-field">{{ old('meta_description', $product?->meta_description) }}</textarea>
                </div>
            </div>
        </section>


        
        <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
            <h2 class="mb-4 font-semibold text-brand-800">Image</h2>
            @if ($product?->image)
                <img src="{{ str_starts_with($product->image, 'http') ? $product->image : asset(ltrim($product->image, '/')) }}" alt="" class="mb-3 h-32 w-full rounded-lg object-cover">
            @endif
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium">Image URL</label>
                    <input type="url" name="image" value="{{ old('image', $product?->image) }}" placeholder="https://..." class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium">Or upload image</label>
                    <input type="file" name="image_file" accept="image/*" class="mt-1 block w-full text-sm">
                </div>
            </div>
        </section>
    </div>

    <div class="space-y-6">
       

        <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
            <h2 class="mb-4 font-semibold text-brand-800">Gifting & collection</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium">Product type *</label>
                    <select name="product_type" required class="input-field">
                        @foreach ($types as $value => $label)
                            <option value="{{ $value }}" @selected(old('product_type', $product?->product_type ?? 'personal') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-500">Controls homepage gifting sections & filters</p>
                </div>
                <div>
                    <label class="block text-sm font-medium">Categories</label>
                    <select name="category_ids[]" multiple class="input-field min-h-[120px]">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(in_array($category->id, old('category_ids', $selectedCategories)))>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-500">Hold Ctrl/Cmd to select multiple</p>
                </div>
                <div>
                    <label class="block text-sm font-medium">Tags (comma separated)</label>
                    <input type="text" name="tags" value="{{ $tagsString }}" placeholder="herbal, gifting, diwali" class="input-field">
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
            <h2 class="mb-4 font-semibold text-brand-800">Pricing & inventory</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium">Price (₹) *</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $product?->price) }}" required class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium">Compare at price (₹)</label>
                    <input type="number" step="0.01" name="compare_at_price" value="{{ old('compare_at_price', $product?->compare_at_price) }}" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium">Stock *</label>
                    <input type="number" name="stock" value="{{ old('stock', $product?->stock ?? 0) }}" required class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium">MOQ (minimum order qty)</label>
                    <input type="number" name="moq" value="{{ old('moq', $product?->moq ?? 1) }}" min="1" class="input-field">
                </div>
            </div>
        </section>
        
        <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
            <h2 class="mb-4 font-semibold text-brand-800">Homepage flags</h2>
            <div class="space-y-2 text-sm">
                <label class="flex items-center gap-2"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product?->is_featured)) class="rounded text-brand-700"> Featured</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="is_bestseller" value="1" @checked(old('is_bestseller', $product?->is_bestseller)) class="rounded text-brand-700"> Bestseller</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="is_trending" value="1" @checked(old('is_trending', $product?->is_trending)) class="rounded text-brand-700"> Trending</label>
            </div>
        </section>
        <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
            <h2 class="mb-4 font-semibold text-brand-800">Publish</h2>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product?->is_active ?? true)) class="rounded border-brand-300 text-brand-700">
                Active on storefront
            </label>
            <div class="mt-4 flex gap-2">
                <button type="submit" class="btn-primary flex-1">Save product</button>
                <a href="{{ route('admin.products.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </section>

    </div>
</div>
