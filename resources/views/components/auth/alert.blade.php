@props(['type' => 'info'])

@php
    $styles = match ($type) {
        'success' => 'border-green-200 bg-green-50 text-green-800',
        'error' => 'border-rose-200 bg-rose-50 text-rose-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-900',
        default => 'border-brand-200 bg-brand-50 text-brand-900',
    };
@endphp

<div {{ $attributes->merge(['class' => "mb-5 rounded-xl border px-4 py-3 text-sm {$styles}"]) }}>
    {{ $slot }}
</div>
