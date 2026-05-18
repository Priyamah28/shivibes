@php $banner = $banner ?? null; @endphp

<div class="max-w-2xl space-y-4 rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
    <div>
        <label class="block text-sm font-medium">Title *</label>
        <input type="text" name="title" value="{{ old('title', $banner?->title) }}" required class="input-field">
    </div>
    <div>
        <label class="block text-sm font-medium">Subtitle</label>
        <input type="text" name="subtitle" value="{{ old('subtitle', $banner?->subtitle) }}" class="input-field">
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">CTA text</label>
            <input type="text" name="cta_text" value="{{ old('cta_text', $banner?->cta_text) }}" class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium">CTA URL</label>
            <input type="text" name="cta_url" value="{{ old('cta_url', $banner?->cta_url) }}" placeholder="/products" class="input-field">
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Placement</label>
            <select name="placement" class="input-field">
                @foreach ($placements as $value => $label)
                    <option value="{{ $value }}" @selected(old('placement', $banner?->placement ?? 'home_hero') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">Sort order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $banner?->sort_order ?? 0) }}" class="input-field">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium">Image URL</label>
        <input type="url" name="image" value="{{ old('image', $banner?->image) }}" class="input-field">
    </div>
    <div>
        <label class="block text-sm font-medium">Or upload image</label>
        <input type="file" name="image_file" accept="image/*" class="mt-1 block w-full text-sm">
    </div>
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner?->is_active ?? true)) class="rounded text-brand-700">
        Active
    </label>
    <div class="flex gap-2">
        <button type="submit" class="btn-primary">Save banner</button>
        <a href="{{ route('admin.banners.index') }}" class="btn-secondary">Cancel</a>
    </div>
</div>
