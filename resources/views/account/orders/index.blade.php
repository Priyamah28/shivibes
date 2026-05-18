@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="font-serif text-3xl font-bold text-brand-900">My Account</h1>
        <p class="mt-1 text-sm text-slate-600">Track orders and delivery status.</p>
    </div>

    <x-account.nav active="orders" />

    @if ($orders->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-brand-200 bg-white p-12 text-center">
            <p class="text-slate-600">You have not placed any orders yet.</p>
            <a href="{{ route('products.index') }}" class="btn-primary mt-4 inline-flex">Start shopping</a>
        </div>
    @else
        <div class="mt-6 overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-brand-50 text-xs uppercase text-brand-800">
                    <tr>
                        <th class="px-4 py-3">Order</th>
                        <th class="px-4 py-3 hidden sm:table-cell">Date</th>
                        <th class="px-4 py-3">Items</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-50">
                    @foreach ($orders as $order)
                        <tr class="hover:bg-brand-50/50">
                            <td class="px-4 py-3 font-medium">{{ $order->order_number }}</td>
                            <td class="px-4 py-3 hidden text-slate-500 sm:table-cell">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3">{{ $order->items_count }}</td>
                            <td class="px-4 py-3">₹{{ number_format($order->total_amount) }}</td>
                            <td class="px-4 py-3">
                                <span class="badge bg-brand-100 text-brand-800">{{ $order->statusLabel() }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('account.orders.show', $order) }}" class="text-brand-700 hover:underline">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $orders->links() }}</div>
    @endif
@endsection
