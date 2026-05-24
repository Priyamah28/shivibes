@php
    $slides = ($banners ?? collect())->isNotEmpty()
        ? $banners->map(fn ($b) => [
            'title' => $b->title,
            'subtitle' => $b->subtitle,
            'cta' => $b->cta_text ?? 'Shop Now',
            'url' => $b->cta_url ?? route('products.index'),
            'image' => $b->image,
        ])
        : collect([
            ['title' => 'Ayurvedic Rituals For Everyday Glow', 'subtitle' => 'Premium herbal skincare inspired by timeless ingredients.', 'cta' => 'Shop New Arrivals', 'url' => route('products.index'), 'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=1800&q=80'],
            ['title' => 'Corporate Gifting Made Effortless', 'subtitle' => 'Bulk orders, custom branding and GST invoicing.', 'cta' => 'Request Quote', 'url' => route('corporate.create'), 'image' => 'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?auto=format&fit=crop&w=1800&q=80'],
        ]);
@endphp

<section
    x-data="{
        current: 0,
        slides: @js($slides->values()),
        next() { this.current = (this.current + 1) % this.slides.length },
        prev() { this.current = (this.current + this.slides.length - 1) % this.slides.length }
    }"
    x-init="if (slides.length > 1) setInterval(() => next(), 6000)"
    class="relative h-[70vh] min-h-[480px] w-full overflow-hidden md:h-[78vh]"
>
    <template x-for="(slide, index) in slides" :key="index">
        <div x-show="current === index" x-transition.opacity class="absolute inset-0">
            <img :src="slide.image" :alt="slide.title" class="h-full w-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-brand-900/80 via-brand-900/50 to-transparent"></div>
            <div class="absolute inset-0 flex items-center">
                <div class="section-container">
                    <div class="max-w-2xl text-white">
                        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-gold-200">Herbal Luxury Skincare</p>
                        <h1 class="mt-4 font-serif text-4xl font-bold leading-tight md:text-6xl" x-text="slide.title"></h1>
                        <p class="mt-5 text-base text-brand-100 md:text-lg" x-text="slide.subtitle"></p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            <a :href="slide.url" class="btn-gold" x-text="slide.cta"></a>
                            <a href="{{ route('products.index', ['type' => 'combo']) }}" class="btn-secondary border-white/30 bg-white/10 text-white backdrop-blur-sm hover:bg-white/20">Combo Packs</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <div class="absolute bottom-8 left-1/2 flex -translate-x-1/2 gap-2">
        <template x-for="(slide, index) in slides" :key="'dot-'+index">
            <button @click="current = index" :class="current === index ? 'w-8 bg-white' : 'w-3 bg-white/40'" class="h-2 rounded-full transition-all"></button>
        </template>
    </div>
</section>
