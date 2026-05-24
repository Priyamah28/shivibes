@props(['title', 'subtitle' => null, 'href' => null, 'linkText' => 'View all', 'eyebrow' => null])

<div {{ $attributes->merge(['class' => 'mb-10 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div class="max-w-2xl">
        @if ($eyebrow)
            <p class="section-eyebrow">{{ $eyebrow }}</p>
        @endif
        <h2 class="section-title {{ $eyebrow ? 'mt-2' : '' }}">{{ $title }}</h2>
        @if ($subtitle)
            <p class="section-subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    @if ($href)
        <a href="{{ $href }}" class="section-link shrink-0">{{ $linkText }} →</a>
    @endif
</div>
