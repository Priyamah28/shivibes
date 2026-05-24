@extends('admin.layout')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-900">Product Reviews</h1>
            <p class="mt-1 text-sm text-slate-600">Approve reviews before they appear on product pages.</p>
        </div>
    </div>

    <div class="mb-6 flex flex-wrap gap-2">
        <a
            href="{{ route('admin.reviews.index', ['status' => 'pending']) }}"
            class="rounded-full px-4 py-2 text-sm font-semibold {{ $status === 'pending' ? 'bg-brand-800 text-white' : 'bg-white text-brand-800 ring-1 ring-brand-200 hover:bg-brand-50' }}"
        >
            Pending ({{ $counts['pending'] }})
        </a>
        <a
            href="{{ route('admin.reviews.index', ['status' => 'approved']) }}"
            class="rounded-full px-4 py-2 text-sm font-semibold {{ $status === 'approved' ? 'bg-brand-800 text-white' : 'bg-white text-brand-800 ring-1 ring-brand-200 hover:bg-brand-50' }}"
        >
            Approved ({{ $counts['approved'] }})
        </a>
        <a
            href="{{ route('admin.reviews.index', ['status' => 'all']) }}"
            class="rounded-full px-4 py-2 text-sm font-semibold {{ $status === 'all' ? 'bg-brand-800 text-white' : 'bg-white text-brand-800 ring-1 ring-brand-200 hover:bg-brand-50' }}"
        >
            All ({{ $counts['all'] }})
        </a>
    </div>

    <div class="space-y-4">
        @forelse ($reviews as $review)
            <article class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-gold-500 text-sm font-semibold">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                            @if ($review->is_approved)
                                <span class="badge bg-emerald-100 text-emerald-800">Approved</span>
                            @else
                                <span class="badge bg-amber-100 text-amber-800">Pending</span>
                            @endif
                            <span class="text-xs text-slate-500">{{ $review->created_at->format('d M Y, h:i A') }}</span>
                        </div>

                        @if ($review->title)
                            <h2 class="mt-2 text-lg font-semibold text-slate-900">{{ $review->title }}</h2>
                        @endif

                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $review->body }}</p>

                        <div class="mt-4 flex flex-wrap gap-4 text-xs text-slate-500">
                            <span><strong class="text-slate-700">Customer:</strong> {{ $review->reviewer_name }}
                                @if ($review->user)
                                    ({{ $review->user->email }})
                                @endif
                            </span>
                            @if ($review->product)
                                <span>
                                    <strong class="text-slate-700">Product:</strong>
                                    <a href="{{ route('products.show', $review->product->slug) }}" target="_blank" rel="noopener" class="text-brand-700 hover:underline">
                                        {{ $review->product->name }}
                                    </a>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-wrap gap-2">
                        @unless ($review->is_approved)
                            <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                                    Approve
                                </button>
                            </form>
                        @endunless
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Delete this review permanently?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-100">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-brand-200 bg-white px-6 py-12 text-center text-slate-500">
                No reviews in this list.
            </div>
        @endforelse
    </div>

    @if ($reviews->hasPages())
        <div class="mt-6">
            {{ $reviews->links() }}
        </div>
    @endif
@endsection
