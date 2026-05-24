<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin | Shivibes' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-brand-50 text-slate-800">
    <div class="min-h-screen lg:flex">
        <aside class="flex w-full flex-col border-b border-brand-800 bg-brand-900 text-white lg:sticky lg:top-0 lg:h-screen lg:w-64 lg:overflow-hidden lg:border-b-0 lg:border-r">
            <div class="shrink-0 p-5">
                <a href="{{ route('admin.dashboard') }}" class="font-serif text-xl font-bold text-gold-300">Shivibes Admin</a>
                <p class="mt-1 text-xs text-brand-200">Store management</p>
            </div>

            <nav class="min-h-0 flex-1 space-y-6 overflow-y-auto px-3 pb-6 text-sm lg:px-5">
                <div>
                    <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-brand-300">Overview</p>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'bg-white/15 text-white' : 'text-brand-100 hover:bg-white/10' }} block rounded-lg px-3 py-2.5">Dashboard</a>
                </div>

                <div>
                    <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-brand-300">Catalog</p>
                    <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'bg-white/15 text-white' : 'text-brand-100 hover:bg-white/10' }} block rounded-lg px-3 py-2.5">Products</a>
                    <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'bg-white/15 text-white' : 'text-brand-100 hover:bg-white/10' }} block rounded-lg px-3 py-2.5">Categories</a>
                </div>

                <div>
                    <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-brand-300">Homepage & Content</p>
                    <a href="{{ route('admin.banners.index') }}" class="{{ request()->routeIs('admin.banners.*') ? 'bg-white/15 text-white' : 'text-brand-100 hover:bg-white/10' }} block rounded-lg px-3 py-2.5">Hero Banners</a>
                    <a href="{{ route('admin.testimonials.index') }}" class="{{ request()->routeIs('admin.testimonials.*') ? 'bg-white/15 text-white' : 'text-brand-100 hover:bg-white/10' }} block rounded-lg px-3 py-2.5">Testimonials</a>
                    <a href="{{ route('admin.faqs.index') }}" class="{{ request()->routeIs('admin.faqs.*') ? 'bg-white/15 text-white' : 'text-brand-100 hover:bg-white/10' }} block rounded-lg px-3 py-2.5">FAQs</a>
                </div>

                <div>
                    <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-brand-300">Users</p>
                    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'bg-white/15 text-white' : 'text-brand-100 hover:bg-white/10' }} block rounded-lg px-3 py-2.5">User Accounts</a>
                </div>

                <div>
                    <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-brand-300">Sales</p>
                    <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'bg-white/15 text-white' : 'text-brand-100 hover:bg-white/10' }} block rounded-lg px-3 py-2.5">Orders</a>
                    <a href="{{ route('admin.inquiries.index') }}" class="{{ request()->routeIs('admin.inquiries.*') ? 'bg-white/15 text-white' : 'text-brand-100 hover:bg-white/10' }} block rounded-lg px-3 py-2.5">Corporate Inquiries</a>
                    <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'bg-white/15 text-white' : 'text-brand-100 hover:bg-white/10' }} block rounded-lg px-3 py-2.5">Product Reviews</a>
                </div>
            </nav>
        </aside>

        <main class="flex-1 p-6 lg:p-8">
            @if (session('success'))
                <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
