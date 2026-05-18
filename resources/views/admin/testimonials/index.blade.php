@extends('admin.layout')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Testimonials',
        'subtitle' => 'Customer stories on the homepage.',
        'actionUrl' => route('admin.testimonials.create'),
        'actionLabel' => '+ Add Testimonial',
    ])

    <div class="space-y-3">
        @forelse ($testimonials as $testimonial)
            <article class="rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="font-semibold">{{ $testimonial->name }} @if($testimonial->location)<span class="text-sm font-normal text-slate-500">· {{ $testimonial->location }}</span>@endif</p>
                        <p class="mt-1 text-sm text-slate-600">"{{ Str::limit($testimonial->body, 120) }}"</p>
                        <p class="mt-2 text-xs text-gold-600">{{ str_repeat('★', $testimonial->rating) }}</p>
                    </div>
                    <div class="shrink-0 text-right">
                        <span class="badge {{ $testimonial->is_active ? 'bg-green-100 text-green-800' : 'bg-slate-100' }}">{{ $testimonial->is_active ? 'Live' : 'Off' }}</span>
                        <div class="mt-2 flex gap-2 justify-end">
                            <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="text-sm text-brand-700">Edit</a>
                            <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="POST" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="text-sm text-rose-600">Delete</button></form>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <p class="rounded-2xl border border-dashed border-brand-200 bg-white p-12 text-center text-slate-500">No testimonials yet.</p>
        @endforelse
    </div>
@endsection
