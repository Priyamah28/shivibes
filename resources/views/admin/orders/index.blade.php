@extends('admin.layout')

@section('content')
    <h1 class="mb-5 text-2xl font-bold">Orders</h1>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3">Order ID</th>
                    <th class="px-4 py-3">Customer</th>
                    <th class="px-4 py-3">Amount</th>
                    <th class="px-4 py-3">Delivery</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ $order->order_number }}</td>
                        <td class="px-4 py-3">{{ $order->customer?->name }}</td>
                        <td class="px-4 py-3">Rs. {{ number_format($order->total_amount) }}</td>
                        <td class="px-4 py-3">{{ ucfirst($order->delivery_type) }}</td>
                        <td class="px-4 py-3">{{ ucfirst($order->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
