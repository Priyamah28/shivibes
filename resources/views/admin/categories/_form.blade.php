@php $category = $category ?? null; @endphp

<div class="max-w-2xl space-y-6 rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
    <div>
        <label class="block text-sm font-medium">Name *</label>
        <input type="text" name="name" value="{{ old('name', $category?->name) }}" required class="input-field">
    </div>
    <div>
        <label class="block text-sm font-medium">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $category?->slug) }}" class="input-field" placeholder="auto-generated">
    </div>
    <div>
        <label class="block text-sm font-medium">Description</label>
        <textarea name="description" rows="3" class="input-field">{{ old('description', $category?->description) }}</textarea>
    </div>
    <x-admin.image-upload
        preset="category"
        :current-url="$category?->image"
    />
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Sort order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $category?->sort_order ?? 0) }}" class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium">Parent category</label>
            <select name="parent_id" class="input-field">
                <option value="">None (top level)</option>
                @foreach ($parents as $parent)
                    @if ($category && $parent->id === $category->id) @continue @endif
                    <option value="{{ $parent->id }}" @selected(old('parent_id', $category?->parent_id) == $parent->id)>{{ $parent->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category?->is_active ?? true)) class="rounded text-brand-700">
        Active on storefront
    </label>
    <div class="flex gap-2 pt-2">
        <button type="submit" class="btn-primary">Save category</button>
        <a href="{{ route('admin.categories.index') }}" class="btn-secondary">Cancel</a>
    </div>
</div>
