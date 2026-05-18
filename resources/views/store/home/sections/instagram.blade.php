<section class="bg-brand-900 py-14 text-white">
    <div class="section-container text-center">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gold-300">@shivibes.herbal</p>
        <h2 class="mt-3 font-serif text-3xl font-semibold">Join Our Community</h2>
        <p class="mx-auto mt-2 max-w-lg text-sm text-brand-100">Follow us for skincare rituals, gifting inspiration and behind-the-scenes from our studio.</p>
        <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-4">
            @foreach ([
                'https://images.unsplash.com/photo-1556228720-195a672e8a03?w=400&q=80',
                'https://images.unsplash.com/photo-1611930022073-b7a4ba5fcccd?w=400&q=80',
                'https://images.unsplash.com/photo-1570194065650-d99fb4f9f0f4?w=400&q=80',
                'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?w=400&q=80',
            ] as $img)
                <div class="aspect-square overflow-hidden rounded-xl">
                    <img src="{{ $img }}" alt="Shivibes social" class="h-full w-full object-cover opacity-90 transition hover:scale-105 hover:opacity-100" loading="lazy">
                </div>
            @endforeach
        </div>
    </div>
</section>
