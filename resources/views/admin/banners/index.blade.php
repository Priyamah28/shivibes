@extends('admin.layout')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Hero Banners',
        'subtitle' => 'Homepage carousel slides.',
        'actionUrl' => route('admin.banners.create'),
        'actionLabel' => '+ Add Banner',
    ])

    <div class="grid gap-4 md:grid-cols-2">
        @forelse ($banners as $banner)
            <article class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
                <img src="{{ str_starts_with($banner->image, 'http') ? $banner->image : asset(ltrim($banner->image, '/')) }}" alt="" class="h-40 w-full object-cover">
                <div class="p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h3 class="font-semibold">{{ $banner->title }}</h3>
                            <p class="text-sm text-slate-500">{{ $banner->subtitle }}</p>
                        </div>
                        <span class="badge {{ $banner->is_active ? 'bg-green-100 text-green-800' : 'bg-slate-100' }}">{{ $banner->is_active ? 'Live' : 'Off' }}</span>
                    </div>
                    <p class="mt-2 text-xs text-slate-500">Order: {{ $banner->sort_order }} · {{ $banner->placement }}</p>
                    <div class="mt-3 flex gap-3">
                        <a href="{{ route('admin.banners.edit', $banner) }}" class="text-sm font-medium text-brand-700">Edit</a>
                        <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Delete banner?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-sm text-rose-600">Delete</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <p class="col-span-2 rounded-2xl border border-dashed border-brand-200 bg-white p-12 text-center text-slate-500">No banners. Add hero slides for the homepage.</p>
        @endforelse
    </div>
@endsection
