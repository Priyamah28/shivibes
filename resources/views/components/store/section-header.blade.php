@props(['title', 'subtitle' => null, 'href' => null, 'linkText' => 'View all'])

<div {{ $attributes->merge(['class' => 'mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div>
        <h2 class="section-title">{{ $title }}</h2>
        @if ($subtitle)
            <p class="section-subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    @if ($href)
        <a href="{{ $href }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">{{ $linkText }} →</a>
    @endif
</div>
