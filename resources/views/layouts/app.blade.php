<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Shivibes Herbal Skincare' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Natural herbal skincare products for healthy and glowing skin.' }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-emerald-50 text-slate-800">
    @php
        $cartCount = collect(session('cart', []))->sum('quantity');
    @endphp

    <header class="sticky top-0 z-30 border-b border-emerald-100 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-emerald-700">Shivibes</a>
            <nav class="flex items-center gap-4 text-sm font-medium">
                <a href="{{ route('home') }}" class="hover:text-emerald-700">Home</a>
                <a href="{{ route('products.index') }}" class="hover:text-emerald-700">Products</a>
                <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-700">Admin</a>

                <a href="{{ route('cart.index') }}" class="relative rounded-full p-2 hover:bg-emerald-50" title="Cart">
                    <svg class="h-6 w-6 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.4 10.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.6L21 7H7.2" />
                        <circle cx="10" cy="20" r="1.5" />
                        <circle cx="18" cy="20" r="1.5" />
                    </svg>
                    @if ($cartCount > 0)
                        <span class="absolute -right-1 -top-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-emerald-600 px-1 text-xs font-bold text-white">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                @auth
                    <a href="{{ route('profile.edit') }}" class="hover:text-emerald-700">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-md border border-emerald-200 px-3 py-1.5 text-emerald-700 hover:bg-emerald-50">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-md border border-emerald-200 px-3 py-1.5 text-emerald-700 hover:bg-emerald-50">Login</a>
                    <a href="{{ route('register') }}" class="rounded-md bg-emerald-600 px-3 py-1.5 text-white hover:bg-emerald-700">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    @if (session('success'))
        <div class="mx-auto mt-4 max-w-6xl rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @php
        $isFullWidth = trim($__env->yieldContent('fullWidth')) === '1';
    @endphp

    <main class="{{ $isFullWidth ? 'w-full py-0' : 'mx-auto w-full max-w-6xl px-4 py-8' }}">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    <footer class="mt-16 border-t border-emerald-100 bg-white">
        <div class="mx-auto grid max-w-6xl gap-4 px-4 py-8 text-sm text-slate-600 md:grid-cols-3">
            <div>
                <h3 class="font-semibold text-slate-800">Shivibes Herbal</h3>
                <p class="mt-2">Natural skincare crafted for healthy, glowing skin.</p>
            </div>
            <div>
                <h3 class="font-semibold text-slate-800">Quick Links</h3>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('products.index') }}" class="block hover:text-emerald-700">Shop Products</a>
                    <a href="{{ route('checkout.index') }}" class="block hover:text-emerald-700">Checkout</a>
                </div>
            </div>
            <div>
                <h3 class="font-semibold text-slate-800">Support</h3>
                <p class="mt-2">WhatsApp: +91-90000-00000</p>
                <p>Email: hello@shivibes.in</p>
            </div>
        </div>
    </footer>
</body>
</html>
