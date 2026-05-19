@extends('layouts.app')

@section('content')
@php
    $statuses = [
        'pending' => ['label' => 'Pending', 'icon' => '📄'],
        'confirmed' => ['label' => 'Confirmed', 'icon' => '✓'],
        'processing' => ['label' => 'Processing', 'icon' => '⚙'],
        'packed' => ['label' => 'Packed', 'icon' => '📦'],
        'shipped' => ['label' => 'Shipped', 'icon' => '🚚'],
        'out_for_delivery' => ['label' => 'Out for Delivery', 'icon' => '📍'],
        'delivered' => ['label' => 'Delivered', 'icon' => '✅'],
    ];

    $currentIndex = array_search($order->status, array_keys($statuses), true);
    $currentIndex = $currentIndex === false ? 0 : $currentIndex;
@endphp

<div class="w-full max-w-full">

    {{-- Full-width header --}}
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <a href="{{ route('account.orders.index') }}"
               class="inline-flex items-center gap-2 text-sm font-medium text-brand-700 hover:text-brand-800">
                ← My Orders
            </a>

            <h1 class="mt-3 font-serif text-3xl font-bold tracking-tight text-brand-900">
                Order {{ $order->order_number }}
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Placed on {{ $order->created_at->format('d M Y, h:i A') }}
            </p>
        </div>

        <div class="inline-flex shrink-0 items-center gap-3 rounded-2xl border border-brand-100 bg-white px-5 py-4 shadow-sm">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-100 text-xl">
                📦
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Current Status</p>
                <p class="text-lg font-semibold text-brand-800">
                    {{ \App\Models\Order::STATUS_LABELS[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status)) }}
                </p>
            </div>
        </div>
    </div>

    {{-- 2-column layout: ~70% main + ~30% sidebar on lg+ --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:items-start">

        {{-- LEFT: Order Status, Items, History --}}
        <div class="min-w-0 space-y-6 lg:col-span-8">

            {{-- Order Status --}}
            <section class="w-full rounded-3xl border border-brand-100 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-brand-900 sm:text-2xl">Order Status</h2>

                <div class="mt-8 -mx-1 overflow-x-auto px-1">
                    <div class="flex min-w-max items-start justify-between gap-2 sm:gap-4">
                        @foreach ($statuses as $key => $status)
                            @php
                                $index = array_search($key, array_keys($statuses), true);
                                $completed = $currentIndex >= $index;
                                $current = $order->status === $key;
                            @endphp

                            <div class="relative flex w-24 flex-shrink-0 flex-col items-center text-center sm:w-28">
                                @if (! $loop->last)
                                    <div class="absolute left-1/2 top-5 h-0.5 w-full {{ $completed ? 'bg-brand-600' : 'bg-slate-200' }}"></div>
                                @endif

                                <div class="relative z-10 flex h-11 w-11 items-center justify-center rounded-full border-2 text-lg
                                    {{ $completed ? 'border-brand-700 bg-brand-700 text-white' : 'border-slate-300 bg-white text-slate-400' }}">
                                    {{ $status['icon'] }}
                                </div>

                                <div class="mt-3 px-1">
                                    <p class="text-xs font-semibold sm:text-sm {{ $completed ? 'text-slate-900' : 'text-slate-400' }}">
                                        {{ $status['label'] }}
                                    </p>
                                    @if ($current)
                                        <p class="mt-1 text-xs font-medium text-brand-700">Current</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-8 rounded-2xl border border-brand-100 bg-brand-50 px-5 py-4">
                    <div class="flex items-start gap-3">
                        <span class="text-lg" aria-hidden="true">🚚</span>
                        <div class="min-w-0">
                            <p class="font-medium text-brand-900">
                                Your order is currently
                                {{ strtolower(\App\Models\Order::STATUS_LABELS[$order->status] ?? $order->status) }}.
                            </p>
                            <p class="mt-1 text-sm text-slate-600">
                                We are processing your shipment and will keep you updated.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Items --}}
            <section class="w-full rounded-3xl border border-brand-100 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-brand-900 sm:text-2xl">
                    Items ({{ $order->items->count() }})
                </h2>

                <div class="mt-6 divide-y divide-brand-50">
                    @foreach ($order->items as $item)
                        <div class="flex items-center justify-between gap-4 py-5">
                            <div class="flex min-w-0 items-center gap-4">
                                <img
                                    src="{{ $item->product?->image ? asset($item->product->image) : 'https://placehold.co/100x100' }}"
                                    alt=""
                                    class="h-16 w-16 shrink-0 rounded-2xl border border-brand-100 bg-white object-cover sm:h-20 sm:w-20"
                                >
                                <div class="min-w-0">
                                    <p class="line-clamp-2 text-base font-semibold text-slate-900">
                                        {{ $item->product?->name ?? 'Product' }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Qty: {{ $item->quantity }} × ₹{{ number_format($item->unit_price) }}
                                    </p>
                                </div>
                            </div>
                            <p class="shrink-0 text-base font-bold text-slate-900 sm:text-lg">
                                ₹{{ number_format($item->line_total) }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Status History --}}
            @if ($order->statusHistories->isNotEmpty())
                <section class="w-full rounded-3xl border border-brand-100 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-brand-900 sm:text-2xl">Status History</h2>

                    <div class="mt-6 space-y-4">
                        @foreach ($order->statusHistories as $history)
                            <div class="flex gap-4 rounded-2xl border border-brand-50 p-4">
                                <div class="flex flex-col items-center">
                                    <div class="h-3 w-3 shrink-0 rounded-full bg-brand-600"></div>
                                    @unless ($loop->last)
                                        <div class="mt-2 min-h-[2rem] w-0.5 flex-1 bg-brand-100"></div>
                                    @endunless
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="min-w-0">
                                            <p class="font-semibold text-slate-900">
                                                {{ \App\Models\Order::STATUS_LABELS[$history->status] ?? ucfirst($history->status) }}
                                            </p>
                                            @if ($history->note)
                                                <p class="mt-1 break-words text-sm text-slate-500">{{ $history->note }}</p>
                                            @endif
                                        </div>
                                        <p class="shrink-0 text-sm text-slate-400">
                                            {{ $history->created_at->format('d M Y, h:i A') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

        </div>

        {{-- RIGHT: Summary, Tracking, Address, Support --}}
        <aside class="min-w-0 space-y-6 lg:col-span-4 lg:sticky lg:top-24 lg:self-start">

            {{-- Order Summary --}}
            <section class="w-full rounded-3xl border border-brand-100 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-brand-900 sm:text-2xl">Order Summary</h2>

                <dl class="mt-6 space-y-4 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Subtotal</dt>
                        <dd class="font-medium">₹{{ number_format($order->subtotal) }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Shipping</dt>
                        <dd class="font-medium">₹{{ number_format($order->shipping_charge) }}</dd>
                    </div>
                    @if ($order->discount_amount > 0)
                        <div class="flex justify-between gap-4 text-green-700">
                            <dt>Discount</dt>
                            <dd class="font-medium">-₹{{ number_format($order->discount_amount) }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between gap-4 border-t border-brand-100 pt-4 text-lg font-bold">
                        <dt>Total</dt>
                        <dd class="text-brand-800">₹{{ number_format($order->total_amount) }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 pt-2">
                        <dt class="text-slate-500">Payment</dt>
                        <dd>
                            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">
                                {{ $order->paymentStatusLabel() }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Delivery</dt>
                        <dd class="font-medium">{{ ucfirst($order->delivery_type) }}</dd>
                    </div>
                </dl>
            </section>

            {{-- Tracking Details --}}
            @if ($order->tracking_number || $order->courier_partner)
                <section class="w-full rounded-3xl border border-brand-100 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-brand-900 sm:text-2xl">Tracking Details</h2>

                    <dl class="mt-6 space-y-4 text-sm">
                        @if ($order->courier_partner)
                            <div class="flex justify-between gap-4">
                                <dt class="shrink-0 text-slate-500">Courier</dt>
                                <dd class="break-words text-right font-medium">{{ $order->courier_partner }}</dd>
                            </div>
                        @endif
                        @if ($order->tracking_number)
                            <div class="flex justify-between gap-4">
                                <dt class="shrink-0 text-slate-500">Tracking ID</dt>
                                <dd class="break-all text-right font-medium">{{ $order->tracking_number }}</dd>
                            </div>
                        @endif
                        @if ($order->shipped_at)
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-500">Shipped</dt>
                                <dd>{{ $order->shipped_at->format('d M Y') }}</dd>
                            </div>
                        @endif
                        @if ($order->delivered_at)
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-500">Delivered</dt>
                                <dd>{{ $order->delivered_at->format('d M Y') }}</dd>
                            </div>
                        @endif
                    </dl>

                    @if ($order->tracking_url)
                        <a href="{{ $order->tracking_url }}"
                           target="_blank"
                           rel="noopener"
                           class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-brand-700 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-800">
                            Track Shipment ↗
                        </a>
                    @endif
                </section>
            @endif

            {{-- Delivery Address --}}
            <section class="w-full rounded-3xl border border-brand-100 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-brand-900 sm:text-2xl">Delivery Address</h2>
                <div class="mt-5 break-words text-sm leading-7 text-slate-600">
                    {!! nl2br(e($order->shippingFormatted())) !!}
                </div>
            </section>

            {{-- Support --}}
            <section class="w-full rounded-3xl border border-brand-100 bg-brand-50 p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white text-xl shadow-sm">
                        🎧
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-lg font-semibold text-brand-900">Need help?</h3>
                        <p class="mt-1 text-sm text-slate-600">
                            Contact our support team for delivery or payment assistance.
                        </p>
                        <a href="https://wa.me/916392086152
                           target="_blank"
                           rel="noopener"
                           class="mt-4 inline-flex items-center rounded-xl bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                            WhatsApp Support
                        </a>
                    </div>
                </div>
            </section>

        </aside>

    </div>
</div>
@endsection
