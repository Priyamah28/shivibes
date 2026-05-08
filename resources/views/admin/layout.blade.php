<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin | Shivibes' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-800">
    <div class="min-h-screen md:flex">
        <aside class="w-full bg-slate-900 p-5 text-slate-100 md:w-64">
            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-emerald-300">Shivibes Admin</a>
            <nav class="mt-6 space-y-2 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="block rounded-md px-3 py-2 hover:bg-slate-800">Dashboard</a>
                <a href="{{ route('admin.products.index') }}" class="block rounded-md px-3 py-2 hover:bg-slate-800">Products</a>
                <a href="{{ route('admin.orders.index') }}" class="block rounded-md px-3 py-2 hover:bg-slate-800">Orders</a>
                <a href="{{ route('home') }}" class="block rounded-md px-3 py-2 hover:bg-slate-800">Back to Store</a>
            </nav>
        </aside>
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
</body>
</html>
