@props(['title', 'actionUrl' => null, 'actionLabel' => 'Add New'])

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="font-serif text-2xl font-bold text-slate-900">{{ $title }}</h1>
        @isset($subtitle)
            <p class="mt-1 text-sm text-slate-600">{{ $subtitle }}</p>
        @endisset
    </div>
    @if ($actionUrl)
        <a href="{{ $actionUrl }}" class="btn-primary shrink-0">{{ $actionLabel }}</a>
    @endif
</div>
