<section class="bg-white py-14">
    <div class="section-container">
        <x-store.section-header title="Shop by Category" subtitle="Face, hair, body, spa and gifting — curated for every ritual." :href="route('products.index')" />
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($categories ?? [] as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="group relative overflow-hidden rounded-2xl">
                    <img src="{{ $category->image }}" alt="{{ $category->name }}" class="h-48 w-full object-cover transition duration-500 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-900/80 to-transparent"></div>
                    <div class="absolute bottom-0 p-5 text-white">
                        <h3 class="font-serif text-xl font-semibold">{{ $category->name }}</h3>
                        <p class="mt-1 text-sm text-brand-100">{{ $category->description }}</p>
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
