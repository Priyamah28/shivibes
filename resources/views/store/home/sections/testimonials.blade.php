<section class="bg-brand-50/70 py-14">
    <div class="section-container">
        <x-store.section-header title="Loved by Our Community" subtitle="Real stories from customers and corporate partners." />
        <div class="grid gap-6 md:grid-cols-3">
            @forelse ($testimonials ?? [] as $testimonial)
                <article class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
                    <div class="mb-3 flex text-gold-500">
                        @for ($i = 0; $i < $testimonial->rating; $i++) ★ @endfor
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed">"{{ $testimonial->body }}"</p>
                    <p class="mt-4 text-sm font-semibold text-slate-900">{{ $testimonial->name }}</p>
                    @if ($testimonial->location)
                        <p class="text-xs text-slate-500">{{ $testimonial->location }}</p>
                    @endif
                </article>
            @empty
                <p class="col-span-3 text-center text-slate-500">Testimonials will appear after seeding store content.</p>
            @endforelse
        </div>
    </div>
</section>
