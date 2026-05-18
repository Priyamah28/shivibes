@extends('admin.layout')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'FAQs',
        'subtitle' => 'Homepage accordion questions.',
        'actionUrl' => route('admin.faqs.create'),
        'actionLabel' => '+ Add FAQ',
    ])

    <div class="space-y-3">
        @forelse ($faqs as $faq)
            <article class="rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
                <div class="flex justify-between gap-4">
                    <div>
                        <span class="badge bg-brand-100 text-brand-800">{{ $faq->category }}</span>
                        <h3 class="mt-2 font-medium">{{ $faq->question }}</h3>
                        <p class="mt-1 text-sm text-slate-600">{{ Str::limit($faq->answer, 100) }}</p>
                    </div>
                    <div class="shrink-0 text-right">
                        <span class="badge {{ $faq->is_active ? 'bg-green-100 text-green-800' : 'bg-slate-100' }}">{{ $faq->is_active ? 'Live' : 'Off' }}</span>
                        <div class="mt-2 space-x-2">
                            <a href="{{ route('admin.faqs.edit', $faq) }}" class="text-sm text-brand-700">Edit</a>
                            <form action="{{ route('admin.faqs.destroy', $faq) }}" class="inline" method="POST" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="text-sm text-rose-600">Delete</button></form>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <p class="rounded-2xl border border-dashed p-12 text-center text-slate-500">No FAQs yet.</p>
        @endforelse
    </div>
@endsection
