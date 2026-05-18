@props(['items' => []])

<nav aria-label="Breadcrumb" class="mb-6 text-sm text-slate-500">
    <ol class="flex flex-wrap items-center gap-1">
        <li><a href="{{ route('home') }}" class="hover:text-brand-700">Home</a></li>
        @foreach ($items as $item)
            <li class="flex items-center gap-1">
                <span class="text-slate-300">/</span>
                @if (!empty($item['url']))
                    <a href="{{ $item['url'] }}" class="hover:text-brand-700">{{ $item['label'] }}</a>
                @else
                    <span class="font-medium text-slate-800">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
