@props([
    'product',
    'reviewStats',
    'userReview' => null,
])

@php
    $count = $reviewStats['count'];
    $average = $reviewStats['average'];
    $breakdown = $reviewStats['breakdown'];
    $maxBar = max(1, max($breakdown));
@endphp

<section id="reviews" class="mt-16 scroll-mt-24">
    <div class="mb-8 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-brand-600">Community</p>
            <h2 class="section-title">Customer Reviews</h2>
            <p class="section-subtitle">Real experiences from Shivibes customers</p>
        </div>
        @if ($count > 0)
            <a href="#write-review" class="text-sm font-semibold text-brand-700 hover:underline">Write a review</a>
        @endif
    </div>

    <div class="grid gap-8 lg:grid-cols-3">
        {{-- Summary --}}
        <div class="review-summary lg:col-span-1">
            @if ($count > 0)
                <p class="font-serif text-5xl font-bold text-brand-900">{{ number_format($average, 1) }}</p>
                <div class="mt-2 flex text-gold-500" aria-label="Average rating {{ $average }} out of 5">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg class="h-5 w-5 {{ $i <= round($average) ? 'fill-current' : 'fill-brand-100 text-brand-200' }}" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <p class="mt-1 text-sm text-slate-600">Based on {{ $count }} {{ Str::plural('review', $count) }}</p>

                <div class="mt-6 space-y-2">
                    @for ($stars = 5; $stars >= 1; $stars--)
                        @php $starCount = $breakdown[$stars] ?? 0; @endphp
                        <div class="flex items-center gap-2 text-xs">
                            <span class="w-8 font-medium text-slate-600">{{ $stars }}★</span>
                            <div class="h-2 flex-1 overflow-hidden rounded-full bg-brand-100">
                                <div
                                    class="h-full rounded-full bg-gold-400 transition-all"
                                    style="width: {{ $count > 0 ? round(($starCount / $count) * 100) : 0 }}%"
                                ></div>
                            </div>
                            <span class="w-6 text-right text-slate-500">{{ $starCount }}</span>
                        </div>
                    @endfor
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-brand-200 bg-white/80 p-6 text-center">
                    <p class="font-serif text-3xl text-brand-300">★★★★★</p>
                    <p class="mt-2 text-sm text-slate-600">No reviews yet. Be the first to share your experience!</p>
                </div>
            @endif
        </div>

        {{-- Reviews list + form --}}
        <div class="space-y-6 lg:col-span-2">
            @if ($product->reviews->isNotEmpty())
                <div class="space-y-4">
                    @foreach ($product->reviews as $review)
                        <article class="review-card">
                            <div class="flex items-start gap-4">
                                <div class="review-avatar" aria-hidden="true">{{ $review->initials() }}</div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ $review->displayName() }}</p>
                                            <p class="text-xs text-slate-500">{{ $review->created_at->format('d M Y') }}</p>
                                        </div>
                                        <div class="flex text-gold-500" aria-label="{{ $review->rating }} out of 5 stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="h-4 w-4 {{ $i <= $review->rating ? 'fill-current' : 'fill-brand-100 text-brand-200' }}" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                    </div>
                                    @if ($review->title)
                                        <p class="mt-2 font-medium text-brand-900">{{ $review->title }}</p>
                                    @endif
                                    <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $review->body }}</p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            <div id="write-review" class="review-form-card">
                <h3 class="font-serif text-xl font-semibold text-brand-900">Share your experience</h3>

                @guest
                    <p class="mt-3 text-sm text-slate-600">
                        <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:underline">Sign in</a>
                        to write a review for {{ $product->name }}.
                    </p>
                @else
                    @if ($userReview)
                        <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                            @if ($userReview->is_approved)
                                <strong>Your review is live.</strong> Thank you for sharing your feedback.
                            @else
                                <strong>Your review is pending approval.</strong> We will publish it once our team has verified it.
                            @endif
                        </div>
                        <article class="review-card mt-4 border-amber-100 bg-amber-50/30">
                            <div class="flex text-gold-500">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="h-4 w-4 {{ $i <= $userReview->rating ? 'fill-current' : 'fill-brand-100' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                            @if ($userReview->title)
                                <p class="mt-2 font-medium text-slate-900">{{ $userReview->title }}</p>
                            @endif
                            <p class="mt-2 text-sm text-slate-600">{{ $userReview->body }}</p>
                        </article>
                    @else
                        <form
                            action="{{ route('products.reviews.store', $product->slug) }}"
                            method="POST"
                            class="mt-5 space-y-5"
                            x-data="{ rating: {{ old('rating', 5) }}, hover: 0 }"
                        >
                            @csrf

                            <div>
                                <label class="block text-sm font-semibold text-slate-800">Your rating</label>
                                <div class="mt-2 flex gap-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <button
                                            type="button"
                                            @click="rating = {{ $i }}"
                                            @mouseenter="hover = {{ $i }}"
                                            @mouseleave="hover = 0"
                                            class="rounded p-0.5 transition hover:scale-110 focus:outline-none focus:ring-2 focus:ring-gold-400"
                                            :aria-label="'Rate {{ $i }} stars'"
                                        >
                                            <svg
                                                class="h-8 w-8"
                                                :class="(hover || rating) >= {{ $i }} ? 'fill-gold-400 text-gold-400' : 'fill-brand-100 text-brand-200'"
                                                viewBox="0 0 20 20"
                                            >
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" x-model="rating">
                                @error('rating')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="review-title" class="block text-sm font-semibold text-slate-800">Headline <span class="font-normal text-slate-500">(optional)</span></label>
                                <input
                                    type="text"
                                    id="review-title"
                                    name="title"
                                    value="{{ old('title') }}"
                                    maxlength="120"
                                    placeholder="Summarize your experience"
                                    class="input-field mt-1"
                                >
                                @error('title')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="review-body" class="block text-sm font-semibold text-slate-800">Your review</label>
                                <textarea
                                    id="review-body"
                                    name="body"
                                    rows="4"
                                    required
                                    minlength="20"
                                    placeholder="What did you love about this product? How was the texture, fragrance, and results?"
                                    class="input-field mt-1"
                                >{{ old('body') }}</textarea>
                                @error('body')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <p class="text-xs text-slate-500">Reviews are moderated. Yours will appear after admin approval.</p>

                            <button type="submit" class="btn-primary">Submit Review</button>
                        </form>
                    @endif
                @endguest
            </div>
        </div>
    </div>
</section>
