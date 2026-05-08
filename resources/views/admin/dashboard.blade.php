@extends('admin.layout')

@section('content')
    <h1 class="mb-6 text-2xl font-bold">Dashboard</h1>

    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <p class="text-sm text-slate-500">Orders Today</p>
            <p class="mt-2 text-2xl font-bold">{{ $stats['orders_today'] }}</p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <p class="text-sm text-slate-500">Pending Orders</p>
            <p class="mt-2 text-2xl font-bold">{{ $stats['pending_orders'] }}</p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <p class="text-sm text-slate-500">Products</p>
            <p class="mt-2 text-2xl font-bold">{{ $stats['products'] }}</p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <p class="text-sm text-slate-500">Monthly Revenue</p>
            <p class="mt-2 text-2xl font-bold">Rs. {{ number_format($stats['revenue_month']) }}</p>
        </div>
    </div>
@endsection
