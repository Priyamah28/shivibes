@php $faq = $faq ?? null; @endphp

<div class="max-w-2xl space-y-4 rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
    <div>
        <label class="block text-sm font-medium">Category</label>
        <select name="category" class="input-field">
            @foreach ($categories as $value => $label)
                <option value="{{ $value }}" @selected(old('category', $faq?->category ?? 'general') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium">Question *</label>
        <input type="text" name="question" value="{{ old('question', $faq?->question) }}" required class="input-field">
    </div>
    <div>
        <label class="block text-sm font-medium">Answer *</label>
        <textarea name="answer" rows="4" required class="input-field">{{ old('answer', $faq?->answer) }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium">Sort order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $faq?->sort_order ?? 0) }}" class="input-field">
    </div>
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $faq?->is_active ?? true)) class="rounded text-brand-700">
        Show on homepage
    </label>
    <div class="flex gap-2">
        <button type="submit" class="btn-primary">Save FAQ</button>
        <a href="{{ route('admin.faqs.index') }}" class="btn-secondary">Cancel</a>
    </div>
</div>
