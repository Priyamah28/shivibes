@extends('admin.layout')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-brand-700 hover:underline">← All orders</a>
        <h1 class="mt-2 font-serif text-2xl font-bold text-slate-900">{{ $order->order_number }}</h1>
        <p class="text-sm text-slate-500">Placed {{ $order->created_at->format('d M Y, h:i A') }}</p>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
                <h2 class="font-semibold">Items ordered</h2>
                <table class="mt-4 min-w-full text-sm">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="py-2 text-left">Product</th>
                            <th class="py-2 text-right">Qty</th>
                            <th class="py-2 text-right">Price</th>
                            <th class="py-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-50">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="py-2">{{ $item->product?->name ?? '—' }}</td>
                                <td class="py-2 text-right">{{ $item->quantity }}</td>
                                <td class="py-2 text-right">₹{{ number_format($item->unit_price) }}</td>
                                <td class="py-2 text-right font-medium">₹{{ number_format($item->line_total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
                <h2 class="font-semibold">Shipping address (snapshot)</h2>
                <p class="mt-3 whitespace-pre-line text-sm text-slate-600">{{ $order->shippingFormatted() }}</p>
            </section>

            <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
                <h2 class="font-semibold">Status history</h2>
                @forelse ($order->statusHistories as $history)
                    <div class="mt-3 border-l-2 border-brand-200 pl-3 text-sm">
                        <p class="font-medium">{{ $statuses[$history->status] ?? $history->status }} · {{ ucfirst($history->payment_status ?? '') }}</p>
                        <p class="text-xs text-slate-500">{{ $history->created_at->format('d M Y, h:i A') }}@if($history->author) · {{ $history->author->name }}@endif</p>
                        @if ($history->note)<p class="text-xs text-slate-600">{{ $history->note }}</p>@endif
                    </div>
                @empty
                    <p class="mt-3 text-sm text-slate-500">No history yet.</p>
                @endforelse
            </section>
        </div>

        <aside class="space-y-6">
            <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm space-y-4">
                @csrf
                @method('PUT')
                <h2 class="font-semibold">Update order</h2>

                <div>
                    <label class="block text-xs font-medium">Order status</label>
                    <select name="status" class="input-field mt-1">
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $order->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium">Payment status</label>
                    <select name="payment_status" class="input-field mt-1">
                        @foreach ($paymentStatuses as $status)
                            <option value="{{ $status }}" @selected(old('payment_status', $order->payment_status) === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium">Courier partner</label>
                    <input type="text" name="courier_partner" value="{{ old('courier_partner', $order->courier_partner) }}" class="input-field mt-1">
                </div>
                <div>
                    <label class="block text-xs font-medium">Tracking ID</label>
                    <input type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" class="input-field mt-1">
                </div>
                <div>
                    <label class="block text-xs font-medium">Tracking URL</label>
                    <input type="url" name="tracking_url" value="{{ old('tracking_url', $order->tracking_url) }}" class="input-field mt-1">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium">Shipped date</label>
                        <input type="date" name="shipped_at" value="{{ old('shipped_at', $order->shipped_at?->format('Y-m-d')) }}" class="input-field mt-1">
                    </div>
                    <div>
                        <label class="block text-xs font-medium">Delivered date</label>
                        <input type="date" name="delivered_at" value="{{ old('delivered_at', $order->delivered_at?->format('Y-m-d')) }}" class="input-field mt-1">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium">Status note (customer-visible in history)</label>
                    <input type="text" name="note" value="{{ old('note') }}" class="input-field mt-1" placeholder="e.g. Handed to courier">
                </div>
                <div>
                    <label class="block text-xs font-medium">Internal notes</label>
                    <textarea name="internal_notes" rows="3" class="input-field mt-1">{{ old('internal_notes', $order->internal_notes) }}</textarea>
                </div>
                <button type="submit" class="btn-primary w-full justify-center">Save changes</button>
            </form>

            <section class="rounded-2xl border border-brand-100 bg-slate-50 p-4 text-sm">
                <p><span class="text-slate-500">Customer:</span> {{ $order->user?->name ?? $order->customer?->name }}</p>
                <p class="mt-1"><span class="text-slate-500">Email:</span> {{ $order->user?->email ?? $order->customer?->email ?? '—' }}</p>
                <p class="mt-1"><span class="text-slate-500">Phone:</span> {{ $order->customer?->phone ?? '—' }}</p>
                <p class="mt-3 font-bold">Total: ₹{{ number_format($order->total_amount) }}</p>
            </section>
        </aside>
    </div>
@endsection
