@props([
    'preset' => 'product',
    'currentUrl' => null,
    'urlField' => 'image',
    'fileField' => 'image_file',
    'urlRequired' => false,
    'dynamicBanner' => false,
    'defaultPlacement' => 'home_hero',
])

@php
    $initialPreset = $preset;
    if ($dynamicBanner) {
        $placement = old('placement', $defaultPlacement);
        $initialPreset = $placement === 'promo_strip' ? 'banner_promo_strip' : 'banner_home_hero';
    }
    $uiSpec = \App\Support\AdminImagePresets::forAdminUi($initialPreset);
    $bannerSpecsJs = $dynamicBanner
        ? [
            'home_hero' => \App\Support\AdminImagePresets::forAdminUi('banner_home_hero'),
            'promo_strip' => \App\Support\AdminImagePresets::forAdminUi('banner_promo_strip'),
        ]
        : null;
@endphp

<div
    class="space-y-3"
    x-data="adminImageUpload({
        spec: @js($uiSpec),
        bannerSpecs: @js($bannerSpecsJs),
        placementField: @js($dynamicBanner ? 'placement' : null),
    })"
>
    <div class="rounded-xl border border-brand-200 bg-brand-50/80 p-4 text-sm">
        <p class="font-semibold text-brand-900" x-text="spec.label + ' — upload requirements'"></p>
        <dl class="mt-3 grid gap-2 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Aspect ratio</dt>
                <dd class="font-medium text-brand-800" x-text="spec.ratio_label"></dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Recommended size</dt>
                <dd class="font-medium text-brand-800" x-text="spec.recommended.width + ' × ' + spec.recommended.height + ' px'"></dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Minimum size</dt>
                <dd class="text-slate-700" x-text="spec.min.width + ' × ' + spec.min.height + ' px'"></dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Maximum size</dt>
                <dd class="text-slate-700" x-text="spec.max.width + ' × ' + spec.max.height + ' px'"></dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Max file size</dt>
                <dd class="text-slate-700" x-text="spec.max_mb + ' MB'"></dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Best on</dt>
                <dd class="text-slate-700">Mobile + laptop</dd>
            </div>
        </dl>
        <p class="mt-3 text-xs text-slate-600" x-text="spec.devices"></p>
        <p class="mt-1 text-xs text-brand-700" x-text="spec.tips"></p>
        <p class="mt-2 text-xs text-slate-500">Formats: JPG, PNG, WebP · Checked in browser before upload and verified again when you save.</p>
    </div>

    @if ($currentUrl)
        <div>
            <p class="mb-1 text-xs font-medium text-slate-500">Current image</p>
            <img
                src="{{ str_starts_with($currentUrl, 'http') ? $currentUrl : asset(ltrim($currentUrl, '/')) }}"
                alt=""
                class="max-h-48 w-full rounded-lg border border-brand-100 object-cover"
            >
        </div>
    @endif

    <div>
        <label class="block text-sm font-medium">Image URL <span class="font-normal text-slate-500">(optional if uploading)</span></label>
        <input
            type="text"
            name="{{ $urlField }}"
            value="{{ old($urlField, $currentUrl) }}"
            placeholder="/storage/... or https://..."
            @if($urlRequired) required @endif
            class="input-field mt-1"
        >
        @error($urlField)
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Upload image</label>
        <input
            type="file"
            name="{{ $fileField }}"
            accept="image/jpeg,image/png,image/webp"
            class="mt-1 block w-full text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-brand-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-brand-800"
            @change="onFileSelect($event)"
        >
        @error($fileField)
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror

        <p x-show="clientMessage" x-text="clientMessage" class="mt-2 text-xs" :class="clientOk ? 'text-brand-700' : 'text-rose-600'"></p>

        <template x-if="previewUrl">
            <div class="mt-3">
                <p class="mb-1 text-xs font-medium text-slate-500">Preview &amp; dimensions</p>
                <img :src="previewUrl" alt="Preview" class="max-h-56 w-full rounded-lg border border-brand-100 object-contain bg-slate-50">
                <p class="mt-1 text-xs text-slate-600" x-text="previewDimensions"></p>
            </div>
        </template>
    </div>
</div>
