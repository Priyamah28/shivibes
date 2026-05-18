@php $testimonial = $testimonial ?? null; @endphp

<div class="max-w-2xl space-y-4 rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Name *</label>
            <input type="text" name="name" value="{{ old('name', $testimonial?->name) }}" required class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium">Location</label>
            <input type="text" name="location" value="{{ old('location', $testimonial?->location) }}" class="input-field">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium">Rating (1–5) *</label>
        <select name="rating" class="input-field">
            @for ($i = 5; $i >= 1; $i--)
                <option value="{{ $i }}" @selected(old('rating', $testimonial?->rating ?? 5) == $i)>{{ $i }} stars</option>
            @endfor
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium">Review *</label>
        <textarea name="body" rows="4" required class="input-field">{{ old('body', $testimonial?->body) }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium">Sort order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $testimonial?->sort_order ?? 0) }}" class="input-field">
    </div>
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $testimonial?->is_active ?? true)) class="rounded text-brand-700">
        Show on homepage
    </label>
    <div class="flex gap-2">
        <button type="submit" class="btn-primary">Save</button>
        <a href="{{ route('admin.testimonials.index') }}" class="btn-secondary">Cancel</a>
    </div>
</div>
