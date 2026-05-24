<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Shivibes — Premium Herbal Skincare & Gifting' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <meta name="description" content="{{ $metaDescription ?? 'Natural herbal skincare, corporate gifting and festival hampers crafted for Indian lifestyles.' }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-brand-50 text-slate-800" x-data>
    <script>
        window.__SHIVIBES = {
            cartCount: {{ (int) ($cartCount ?? 0) }},
            cartQuantities: @json($cartQuantities ?? []),
            wishlistCount: {{ (int) ($wishlistCount ?? 0) }},
            wishlistSlugs: @json($wishlistSlugs ?? []),
        };
    </script>

    {{-- Announcement bar --}}
    <div class="announcement-bar">
        <p class="px-4 py-2.5">
            <span class="text-gold-300">✦</span>
            Free shipping on orders above ₹999
            <span class="mx-2 text-brand-400">·</span>
            Corporate bulk orders —
            <a href="{{ route('corporate.create') }}" class="font-semibold text-gold-200 underline-offset-2 hover:text-white hover:underline">Get a quote</a>
            <span class="text-gold-300">✦</span>
        </p>
    </div>

    {{-- Sticky header --}}
    <header
        class="sticky top-0 z-40 border-b backdrop-blur-md transition-shadow duration-300"
        :class="scrolled ? 'border-brand-200/80 bg-white/90 shadow-glow backdrop-blur-xl' : 'border-transparent bg-white/80 backdrop-blur-lg'"
        x-data="{ mobileOpen: false, scrolled: false }"
        x-init="scrolled = window.scrollY > 8; window.addEventListener('scroll', () => { scrolled = window.scrollY > 8 }, { passive: true })"
    >
        <div class="section-container flex items-center justify-between gap-4 py-4">
            <a href="{{ route('home') }}" class="group flex items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-brand-700 to-brand-600 font-serif text-sm font-bold text-white shadow-md ring-2 ring-brand-100 transition group-hover:scale-105">S</span>
                <span class="font-serif text-2xl font-bold tracking-tight text-brand-900 md:text-3xl">Shivibes</span>
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden items-center gap-7 lg:flex" x-data="{ shopOpen: false }">
                <a href="{{ route('home') }}" class="nav-link">Home</a>

                <div class="relative" @mouseenter="shopOpen = true" @mouseleave="shopOpen = false">
                    <button class="nav-link flex items-center gap-1">
                        Shop
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="shopOpen" x-transition class="glass-panel absolute left-0 top-full z-50 mt-3 w-[520px] p-6" style="display:none;">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-brand-600">Categories</p>
                                @foreach ($navCategories ?? [] as $cat)
                                    <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="block py-1.5 text-sm hover:text-brand-700">{{ $cat->name }}</a>
                                @endforeach
                            </div>
                            <div>
                                <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-brand-600">Collections</p>
                                <a href="{{ route('products.index', ['type' => 'personal']) }}" class="block py-1.5 text-sm hover:text-brand-700">Personal Gifting</a>
                                <a href="{{ route('products.index', ['type' => 'corporate']) }}" class="block py-1.5 text-sm hover:text-brand-700">Corporate Gifting</a>
                                <a href="{{ route('products.index', ['type' => 'festival']) }}" class="block py-1.5 text-sm hover:text-brand-700">Festival Hampers</a>
                                <a href="{{ route('products.index', ['type' => 'combo']) }}" class="block py-1.5 text-sm hover:text-brand-700">Combo Packs</a>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('corporate.create') }}" class="nav-link">Corporate</a>
                <a href="{{ route('products.index') }}" class="nav-link">All Products</a>
                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">Admin</a>
                    @endif
                @endauth
            </nav>

            {{-- Search --}}
            <div class="hidden flex-1 max-w-md lg:block" x-data="searchBox">
                <div class="relative">
                    <input type="search" x-model="query" @input.debounce.300ms="search" placeholder="Search herbal skincare…" class="input-field w-full rounded-full border-brand-200/80 bg-white/90 py-2.5 pl-11 pr-4 shadow-sm">
                    <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <div x-show="open" @click.outside="open = false" class="absolute left-0 right-0 top-full z-50 mt-2 overflow-hidden rounded-xl border border-brand-100 bg-white shadow-lg" style="display:none;">
                        <template x-for="item in results" :key="item.slug">
                            <a :href="item.url" class="flex items-center gap-3 border-b border-brand-50 px-4 py-3 hover:bg-brand-50">
                                <img :src="item.image" :alt="item.name" class="h-10 w-10 rounded-lg object-cover">
                                <div>
                                    <p class="text-sm font-medium" x-text="item.name"></p>
                                    <p class="text-xs text-brand-700" x-text="'₹' + item.price.toLocaleString()"></p>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 sm:gap-3">
                @auth
                    <a href="{{ route('wishlist.index') }}" class="relative hidden rounded-full p-2 hover:bg-brand-50 sm:inline-flex" title="Wishlist">
                        <svg class="h-5 w-5 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 0 0 0 6.364L12 20.364l7.682-7.682a4.5 4.5 0 0 0-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 0 0-6.364 0z"/></svg>
                        <span
                            x-show="$store.nav.wishlistCount > 0"
                            x-text="$store.nav.wishlistCount"
                            class="absolute -right-0.5 -top-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1 text-xs font-bold text-white"
                        ></span>
                    </a>
                @endauth

                @auth
                    <button type="button" @click="$dispatch('toggle-cart')" class="relative rounded-full p-2 hover:bg-brand-50" title="Cart">
                        <svg class="h-6 w-6 text-brand-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.4 10.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.6L21 7H7.2" />
                            <circle cx="10" cy="20" r="1.5" /><circle cx="18" cy="20" r="1.5" />
                        </svg>
                        <span
                            x-show="$store.nav.cartCount > 0"
                            x-text="$store.nav.cartCount"
                            x-transition
                            class="absolute -right-0.5 -top-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-gold-500 px-1 text-xs font-bold text-white"
                        ></span>
                    </button>
                @else
                    <a href="{{ route('cart.index') }}" class="relative rounded-full p-2 hover:bg-brand-50">
                        <svg class="h-6 w-6 text-brand-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.4 10.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.6L21 7H7.2" />
                            <circle cx="10" cy="20" r="1.5" /><circle cx="18" cy="20" r="1.5" />
                        </svg>
                    </a>
                @endauth

                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="hidden text-sm font-medium hover:text-brand-700 md:inline">Admin</a>
                    @endif
                    <a href="{{ route('account.orders.index') }}" class="hidden text-sm font-medium hover:text-brand-700 md:inline">My Orders</a>
                    <a href="{{ route('account.addresses.index') }}" class="hidden text-sm font-medium hover:text-brand-700 lg:inline">Addresses</a>
                    <a href="{{ route('profile.edit') }}" class="hidden text-sm font-medium hover:text-brand-700 md:inline">Profile</a>
                    <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                        @csrf
                        <button class="btn-ghost text-xs">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost hidden sm:inline-flex">Login</a>
                    <a href="{{ route('register') }}" class="btn-primary hidden shadow-md sm:inline-flex">Register</a>
                @endauth

                {{-- Mobile menu toggle --}}
                <button
                    type="button"
                    class="rounded-lg p-2 hover:bg-brand-50 lg:hidden"
                    aria-label="Toggle menu"
                    :aria-expanded="mobileOpen.toString()"
                    @click="mobileOpen = !mobileOpen"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        <div
            id="mobile-nav-panel"
            x-show="mobileOpen"
            x-transition
            @click.outside="mobileOpen = false"
            class="border-t border-brand-100 bg-white lg:hidden"
            style="display: none;"
        >
            <nav class="section-container space-y-1 py-4 text-sm font-medium">
                <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2.5 hover:bg-brand-50" @click="mobileOpen = false">Home</a>
                <a href="{{ route('products.index') }}" class="block rounded-lg px-3 py-2.5 hover:bg-brand-50" @click="mobileOpen = false">All Products</a>
                <a href="{{ route('products.index', ['type' => 'personal']) }}" class="block rounded-lg px-3 py-2.5 hover:bg-brand-50" @click="mobileOpen = false">Personal Gifting</a>
                <a href="{{ route('products.index', ['type' => 'corporate']) }}" class="block rounded-lg px-3 py-2.5 hover:bg-brand-50" @click="mobileOpen = false">Corporate Gifting</a>
                <a href="{{ route('products.index', ['type' => 'festival']) }}" class="block rounded-lg px-3 py-2.5 hover:bg-brand-50" @click="mobileOpen = false">Festival Hampers</a>
                <a href="{{ route('corporate.create') }}" class="block rounded-lg px-3 py-2.5 hover:bg-brand-50" @click="mobileOpen = false">Corporate</a>
                @auth
                    <a href="{{ route('account.orders.index') }}" class="block rounded-lg px-3 py-2.5 hover:bg-brand-50" @click="mobileOpen = false">My Orders</a>
                    <a href="{{ route('account.addresses.index') }}" class="block rounded-lg px-3 py-2.5 hover:bg-brand-50" @click="mobileOpen = false">Addresses</a>
                    <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2.5 hover:bg-brand-50" @click="mobileOpen = false">Profile</a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2.5 hover:bg-brand-50" @click="mobileOpen = false">Admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="pt-2">
                        @csrf
                        <button type="submit" class="w-full rounded-lg px-3 py-2.5 text-left hover:bg-brand-50">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2.5 hover:bg-brand-50" @click="mobileOpen = false">Login</a>
                    <a href="{{ route('register') }}" class="block rounded-lg px-3 py-2.5 hover:bg-brand-50" @click="mobileOpen = false">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    @php $isFullWidth = trim($__env->yieldContent('fullWidth')) === '1'; @endphp

    <main class="{{ $isFullWidth ? 'w-full' : 'section-container py-8' }}">
        @if (session('success'))
            <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
        @endif
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    {{-- Footer --}}
    <footer class="mt-20 border-t border-brand-100/80 bg-gradient-to-b from-white to-brand-50/80">
        <div class="section-container grid gap-10 py-12 md:grid-cols-2 lg:grid-cols-4">
            <div>
                <p class="font-serif text-2xl font-bold text-brand-800">Shivibes</p>
                <p class="mt-3 text-sm text-slate-600">Premium herbal skincare and thoughtful gifting — rooted in Ayurveda, designed for modern India.</p>
                <a href="https://wa.me/916392086152" target="_blank" rel="noopener" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-brand-700 hover:text-brand-800">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492l4.606-1.447A11.94 11.94 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>
                    Chat on WhatsApp
                </a>
            </div>
            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-800">Shop</h3>
                <ul class="mt-4 space-y-2 text-sm text-slate-600">
                    <li><a href="{{ route('products.index') }}" class="hover:text-brand-700">All Products</a></li>
                    <li><a href="{{ route('products.index', ['type' => 'combo']) }}" class="hover:text-brand-700">Combo Packs</a></li>
                    <li><a href="{{ route('products.index', ['type' => 'festival']) }}" class="hover:text-brand-700">Festival Gifts</a></li>
                    <li><a href="{{ route('corporate.create') }}" class="hover:text-brand-700">Corporate Gifting</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-800">Support</h3>
                <ul class="mt-4 space-y-2 text-sm text-slate-600">
                    <li>hello@shivibes.in</li>
                    <li>India Post & Shiprocket delivery</li>
                    <li>7-day return on unopened items</li>
                    <li><a href="{{ route('checkout.index') }}" class="hover:text-brand-700">Checkout</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-800">Newsletter</h3>
                <p class="mt-4 text-sm text-slate-600">Get offers, rituals & new launch updates.</p>
                <form action="{{ route('newsletter.store') }}" method="POST" class="mt-4 flex gap-2">
                    @csrf
                    <input type="email" name="email" required placeholder="Your email" class="input-field flex-1 rounded-full">
                    <button type="submit" class="btn-primary shrink-0 px-4 py-2 text-xs">Join</button>
                </form>
            </div>
        </div>
        <div class="border-t border-brand-100 py-4 text-center text-xs text-slate-500">
            © {{ date('Y') }} Shivibes Herbal. All rights reserved.
        </div>
    </footer>

    @auth
        @include('store.partials.cart-drawer')
    @endauth

    <x-store.toast />

    @stack('scripts')
</body>
</html>
