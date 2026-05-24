@extends('admin.layout')

@section('content')
    <h1 class="mb-6 font-serif text-2xl font-bold">Dashboard</h1>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Orders Today', 'value' => $stats['orders_today']],
            ['label' => 'Pending Orders', 'value' => $stats['pending_orders']],
            ['label' => 'Monthly Revenue', 'value' => '₹' . number_format($stats['revenue_month'])],
            ['label' => 'Products', 'value' => $stats['products']],
            ['label' => 'Low Stock Alerts', 'value' => $stats['low_stock'], 'alert' => $stats['low_stock'] > 0],
            ['label' => 'Customers', 'value' => $stats['customers']],
            ['label' => 'Pending Inquiries', 'value' => $stats['pending_inquiries'], 'alert' => $stats['pending_inquiries'] > 0],
            ['label' => 'Pending Reviews', 'value' => $stats['pending_reviews'], 'alert' => $stats['pending_reviews'] > 0, 'href' => route('admin.reviews.index')],
        ] as $card)
            @if (!empty($card['href']))
                <a href="{{ $card['href'] }}" class="block rounded-xl bg-white p-5 shadow-sm transition hover:shadow-md {{ !empty($card['alert']) ? 'border-l-4 border-amber-500' : '' }}">
                    <p class="text-sm text-slate-500">{{ $card['label'] }}</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $card['value'] }}</p>
                </a>
            @else
                <div class="rounded-xl bg-white p-5 shadow-sm {{ !empty($card['alert']) ? 'border-l-4 border-amber-500' : '' }}">
                    <p class="text-sm text-slate-500">{{ $card['label'] }}</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $card['value'] }}</p>
                </div>
            @endif
        @endforeach
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <h2 class="mb-4 font-semibold">Recent Orders</h2>
            @forelse ($recentOrders as $order)
                <div class="flex items-center justify-between border-b border-slate-100 py-3 text-sm last:border-0">
                    <div>
                        <p class="font-medium">{{ $order->order_number }}</p>
                        <p class="text-slate-500">{{ $order->customer->name ?? 'Guest' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">₹{{ number_format($order->total_amount) }}</p>
                        <span class="badge bg-brand-100 text-brand-800">{{ $order->status }}</span>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No orders yet.</p>
            @endforelse
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm">
            <h2 class="mb-4 font-semibold">Top Products</h2>
            @forelse ($topProducts as $item)
                <div class="flex items-center justify-between border-b border-slate-100 py-3 text-sm last:border-0">
                    <span>{{ $item->name }}</span>
                    <span class="font-semibold">{{ $item->total_sold }} sold</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">No sales data yet.</p>
            @endforelse
        </div>
    </div>
@endsection
