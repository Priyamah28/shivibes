@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <a href="{{ route('account.orders.index') }}" class="text-sm text-brand-700 hover:underline">← My Orders</a>
        <h1 class="mt-2 font-serif text-3xl font-bold text-brand-900">Order {{ $order->order_number }}</h1>
        <p class="mt-1 text-sm text-slate-600">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
    </div>

    <div class="grid gap-8 lg:grid-cols-2">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-brand-900">Order Status</h2>
                <div class="mt-4">
                    <x-store.order-timeline :order="$order" />
                </div>
            </section>


            <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-brand-900">Items</h2>
                <div class="mt-4 divide-y divide-brand-50">
                    @foreach ($order->items as $item)
                        <div class="flex items-center justify-between gap-4 py-3">
                            <div>
                                <p class="font-medium">{{ $item->product?->name ?? 'Product' }}</p>
                                <p class="text-xs text-slate-500">Qty: {{ $item->quantity }} × ₹{{ number_format($item->unit_price) }}</p>
                            </div>
                            <p class="font-semibold">₹{{ number_format($item->line_total) }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            @if ($order->statusHistories->isNotEmpty())
                <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
                    <h2 class="font-semibold text-brand-900">Status History</h2>
                    <ul class="mt-4 space-y-3 text-sm">
                        @foreach ($order->statusHistories as $history)
                            <li class="border-l-2 border-brand-200 pl-3">
                                <p class="font-medium">{{ \App\Models\Order::STATUS_LABELS[$history->status] ?? ucfirst($history->status) }}</p>
                                <p class="text-xs text-slate-500">{{ $history->created_at->format('d M Y, h:i A') }}@if($history->note) · {{ $history->note }}@endif</p>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </div>

        <aside class="space-y-6">
            <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-brand-900">Summary</h2>
                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd>₹{{ number_format($order->subtotal) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Shipping</dt><dd>₹{{ number_format($order->shipping_charge) }}</dd></div>
                    @if ($order->discount_amount > 0)
                        <div class="flex justify-between text-green-700"><dt>Discount</dt><dd>-₹{{ number_format($order->discount_amount) }}</dd></div>
                    @endif
                    <div class="flex justify-between border-t border-brand-100 pt-2 text-base font-bold">
                        <dt>Total</dt><dd class="text-brand-800">₹{{ number_format($order->total_amount) }}</dd>
                    </div>
                    <div class="flex justify-between pt-2"><dt class="text-slate-500">Payment</dt><dd><span class="badge bg-brand-100">{{ $order->paymentStatusLabel() }}</span></dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Delivery</dt><dd>{{ ucfirst($order->delivery_type) }}</dd></div>
                </dl>
            </section>

            @if ($order->tracking_number || $order->courier_partner)
                <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
                    <h2 class="font-semibold text-brand-900">Tracking</h2>
                    <dl class="mt-4 space-y-2 text-sm">
                        @if ($order->courier_partner)
                            <div class="flex justify-between gap-4"><dt class="text-slate-500">Courier</dt><dd class="font-medium">{{ $order->courier_partner }}</dd></div>
                        @endif
                        @if ($order->tracking_number)
                            <div class="flex justify-between gap-4"><dt class="text-slate-500">Tracking ID</dt><dd class="font-medium">{{ $order->tracking_number }}</dd></div>
                        @endif
                        @if ($order->shipped_at)
                            <div class="flex justify-between gap-4"><dt class="text-slate-500">Shipped</dt><dd>{{ $order->shipped_at->format('d M Y') }}</dd></div>
                        @endif
                        @if ($order->delivered_at)
                            <div class="flex justify-between gap-4"><dt class="text-slate-500">Delivered</dt><dd>{{ $order->delivered_at->format('d M Y') }}</dd></div>
                        @endif
                    </dl>
                    @if ($order->tracking_url)
                        <a href="{{ $order->tracking_url }}" target="_blank" rel="noopener" class="btn-primary mt-4 inline-flex text-sm">Track shipment ↗</a>
                    @endif
                </section>
            @endif
            <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-brand-900">Delivery Address</h2>
                <p class="mt-3 whitespace-pre-line text-sm text-slate-600">{{ $order->shippingFormatted() }}</p>
            </section>
        </aside>
    </div>
@endsection
