@extends('layouts.app')

@section('fullWidth', '1')

@section('content')
    <section
        x-data="{
            current: 0,
            slides: [
                {
                    title: 'Ayurvedic Rituals For Everyday Glow',
                    subtitle: 'Premium herbal skincare inspired by timeless ingredients and crafted for modern Indian lifestyles.',
                    cta: 'Shop New Arrivals',
                    image: 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=1800&q=80'
                },
                {
                    title: 'Face, Body, Hair & Spa Essentials',
                    subtitle: 'Build your complete natural self-care routine with products that feel luxurious and effective.',
                    cta: 'Explore Categories',
                    image: 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=1800&q=80'
                },
                {
                    title: 'Clean Formulas. Stunning Skin Results.',
                    subtitle: 'No harsh chemicals. Only thoughtful botanical care with a soft, premium shopping experience.',
                    cta: 'View Bestsellers',
                    image: 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=1800&q=80'
                }
            ],
            next() { this.current = (this.current + 1) % this.slides.length },
            prev() { this.current = (this.current + this.slides.length - 1) % this.slides.length }
        }"
        x-init="setInterval(() => next(), 5000)"
        class="relative h-[72vh] min-h-[520px] w-full overflow-hidden"
    >
        <template x-for="(slide, index) in slides" :key="index">
            <div
                x-show="current === index"
                x-transition:enter="transition-opacity duration-700"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                class="absolute inset-0"
            >
                <img :src="slide.image" :alt="slide.title" class="h-full w-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/45 to-black/20"></div>
                <div class="absolute inset-0 flex items-center">
                    <div class="mx-auto w-full max-w-7xl px-6">
                        <div class="max-w-2xl text-white">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-emerald-200">Herbal Luxury Skincare</p>
                            <h1 class="mt-4 text-4xl font-bold leading-tight md:text-6xl" x-text="slide.title"></h1>
                            <p class="mt-5 text-base text-slate-100 md:text-lg" x-text="slide.subtitle"></p>
                            <div class="mt-8 flex flex-wrap gap-3">
                                <a href="{{ route('products.index') }}" class="rounded-full bg-emerald-500 px-7 py-3 text-sm font-semibold text-white transition hover:bg-emerald-600" x-text="slide.cta"></a>
                                <a href="{{ route('checkout.index') }}" class="rounded-full border border-white/70 px-7 py-3 text-sm font-semibold text-white transition hover:bg-white/15">Checkout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="absolute bottom-8 left-1/2 flex -translate-x-1/2 items-center gap-2">
            <template x-for="(slide, index) in slides" :key="'dot-' + index">
                <button @click="current = index" :class="current === index ? 'bg-white' : 'bg-white/45'" class="h-2.5 w-8 rounded-full transition"></button>
            </template>
        </div>

        <button @click="prev()" class="absolute left-4 top-1/2 hidden -translate-y-1/2 rounded-full bg-white/25 p-3 text-white backdrop-blur transition hover:bg-white/40 md:block">‹</button>
        <button @click="next()" class="absolute right-4 top-1/2 hidden -translate-y-1/2 rounded-full bg-white/25 p-3 text-white backdrop-blur transition hover:bg-white/40 md:block">›</button>
    </section>

    <section class="bg-white py-10">
        <div class="mx-auto grid w-full max-w-7xl gap-4 px-6 md:grid-cols-4">
            <article class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Skin</h3>
                <p class="mt-2 text-sm text-slate-600">Face wash, creams, masks and glow rituals.</p>
            </article>
            <article class="rounded-2xl border border-amber-100 bg-amber-50 p-5">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-amber-700">Hair</h3>
                <p class="mt-2 text-sm text-slate-600">Shampoo, oil and frizz control essentials.</p>
            </article>
            <article class="rounded-2xl border border-rose-100 bg-rose-50 p-5">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-rose-700">Body & Bath</h3>
                <p class="mt-2 text-sm text-slate-600">Daily body care and deeply nourishing soaps.</p>
            </article>
            <article class="rounded-2xl border border-indigo-100 bg-indigo-50 p-5">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-indigo-700">Spa</h3>
                <p class="mt-2 text-sm text-slate-600">Relaxing home-spa products for weekend reset.</p>
            </article>
        </div>
    </section>

    <section class="bg-emerald-50/70 py-14">
        <div class="mx-auto w-full max-w-7xl px-6">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-3xl font-semibold text-slate-900">New Arrivals</h2>
                <a href="{{ route('products.index') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">View all products</a>
            </div>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($newArrivals as $product)
                    <article class="group overflow-hidden rounded-2xl border border-emerald-100 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="overflow-hidden">
                            <img src="{{ $product->image }}" alt="{{ $product->name }}" class="h-56 w-full object-cover transition duration-500 group-hover:scale-105">
                        </div>
                        <div class="p-5">
                            <h3 class="line-clamp-1 text-base font-semibold">{{ $product->name }}</h3>
                            <p class="mt-1 line-clamp-2 text-sm text-slate-600">{{ $product->description }}</p>
                            <div class="mt-4 flex items-center justify-between">
                                <span class="font-bold text-emerald-700">Rs. {{ number_format($product->price) }}</span>
                                <a href="{{ route('products.show', $product->slug) }}" class="rounded-full bg-emerald-600 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-700">Shop now</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white py-14">
        <div class="mx-auto w-full max-w-7xl px-6">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-3xl font-semibold text-slate-900">Our Bestsellers</h2>
                <a href="{{ route('products.index') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">Shop collection</a>
            </div>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($bestSellers as $product)
                    <article class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="h-52 w-full object-cover">
                        <div class="p-5">
                            <div class="mb-2 inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">Popular</div>
                            <h3 class="line-clamp-1 text-base font-semibold">{{ $product->name }}</h3>
                            <div class="mt-4 flex items-center justify-between">
                                <span class="font-bold text-slate-800">Rs. {{ number_format($product->price) }}</span>
                                <a href="{{ route('products.show', $product->slug) }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">View</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-gradient-to-r from-emerald-800 to-emerald-700 py-14 text-white">
        <div class="mx-auto grid w-full max-w-7xl gap-8 px-6 md:grid-cols-2">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-emerald-200">Why Shivibes</p>
                <h2 class="mt-3 text-3xl font-semibold md:text-4xl">Tradition Meets Premium Modern Experience</h2>
                <p class="mt-4 text-emerald-100">
                    We craft high-quality herbal skincare for Indian skin needs with a clean, trustworthy, and beautiful shopping experience.
                </p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <article class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur">
                    <h3 class="font-semibold">Cruelty Free</h3>
                    <p class="mt-2 text-sm text-emerald-100">Consciously formulated and responsibly sourced.</p>
                </article>
                <article class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur">
                    <h3 class="font-semibold">Safe Formulas</h3>
                    <p class="mt-2 text-sm text-emerald-100">No harsh ingredients, only skin-loving care.</p>
                </article>
                <article class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur">
                    <h3 class="font-semibold">Fast Delivery</h3>
                    <p class="mt-2 text-sm text-emerald-100">India Post and Shiprocket-based delivery options.</p>
                </article>
                <article class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur">
                    <h3 class="font-semibold">Secure Checkout</h3>
                    <p class="mt-2 text-sm text-emerald-100">Simple checkout flow for faster conversions.</p>
                </article>
            </div>
        </div>
    </section>
@endsection
