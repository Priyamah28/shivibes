@extends('admin.layout')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Orders',
        'subtitle' => 'Manage order status, payments, and shipment tracking.',
    ])

    <form method="GET" class="mb-4 flex flex-wrap items-end gap-3 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
        <div class="min-w-[200px] flex-1">
            <label class="block text-xs font-medium text-slate-600">Search</label>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Order #, name, email, phone…" class="input-field mt-1">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600">Order status</label>
            <select name="status" class="input-field mt-1">
                <option value="">All</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600">Payment</label>
            <select name="payment_status" class="input-field mt-1">
                <option value="">All</option>
                @foreach ($paymentStatuses as $status)
                    <option value="{{ $status }}" @selected(request('payment_status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary">Filter</button>
        @if (request()->hasAny(['q', 'status', 'payment_status']))
            <a href="{{ route('admin.orders.index') }}" class="btn-ghost">Clear</a>
        @endif
    </form>

    <div class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-brand-50 text-xs uppercase text-brand-800">
                <tr>
                    <th class="px-4 py-3">Order</th>
                    <th class="px-4 py-3">Customer</th>
                    <th class="px-4 py-3">Amount</th>
                    <th class="px-4 py-3">Payment</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-50">
                @forelse ($orders as $order)
                    <tr class="hover:bg-brand-50/50">
                        <td class="px-4 py-3 font-medium">{{ $order->order_number }}</td>
                        <td class="px-4 py-3">
                            <p>{{ $order->user?->name ?? $order->customer?->name }}</p>
                            <p class="text-xs text-slate-500">{{ $order->user?->email ?? $order->customer?->phone }}</p>
                        </td>
                        <td class="px-4 py-3">₹{{ number_format($order->total_amount) }}</td>
                        <td class="px-4 py-3"><span class="badge bg-brand-100">{{ ucfirst($order->payment_status) }}</span></td>
                        <td class="px-4 py-3"><span class="badge bg-gold-100 text-gold-900">{{ $order->statusLabel() }}</span></td>
                        <td class="px-4 py-3 text-slate-500">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-brand-700 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-slate-500">No orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($orders->hasPages())
        <div class="mt-4">{{ $orders->links() }}</div>
    @endif
@endsection
