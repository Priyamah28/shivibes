<section class="py-16 md:py-20">
    <div class="section-container">
        <x-store.section-header eyebrow="Curated for you" title="Shop by Category" subtitle="Face, hair, body, spa and gifting — rituals for every moment." :href="route('products.index')" />
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($categories ?? [] as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="group relative overflow-hidden rounded-2xl shadow-card ring-1 ring-brand-100/50 transition duration-500 hover:-translate-y-1 hover:shadow-premium hover:ring-brand-200/60">
                    <img src="{{ $category->image }}" alt="{{ $category->name }}" class="h-52 w-full object-cover transition duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-950/85 via-brand-900/20 to-transparent"></div>
                    <div class="absolute bottom-0 p-6 text-white">
                        <h3 class="font-serif text-2xl font-semibold">{{ $category->name }}</h3>
                        <p class="mt-2 line-clamp-2 text-sm text-brand-100/90">{{ $category->description }}</p>
                        <span class="mt-3 inline-flex text-xs font-semibold uppercase tracking-wider text-gold-300 opacity-0 transition group-hover:opacity-100">Explore →</span>
                    </div>
                </a>
            @empty
                @foreach (['Face Care', 'Hair Care', 'Body & Bath', 'Spa & Wellness'] as $name)
                    <article class="rounded-2xl border border-brand-100 bg-brand-50 p-6">
                        <h3 class="font-semibold text-brand-800">{{ $name }}</h3>
                    </article>
                @endforeach
            @endforelse
        </div>
    </div>
</section>
