@props(['order', 'compact' => false])

@php
    $currentIndex = $order->statusIndex();
    $cancelled = in_array($order->status, [\App\Models\Order::STATUS_CANCELLED, \App\Models\Order::STATUS_RETURNED], true);
@endphp

<div class="{{ $compact ? 'space-y-2' : 'space-y-4' }}">
    @if ($cancelled)
        <p class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            This order was marked as <strong>{{ $order->statusLabel() }}</strong>.
        </p>
    @else
        <ol class="{{ $compact ? 'space-y-2' : 'relative border-l border-brand-200 pl-6' }}">
            @foreach (\App\Models\Order::STATUSES as $index => $status)
                @if (in_array($status, [\App\Models\Order::STATUS_CANCELLED, \App\Models\Order::STATUS_RETURNED], true))
                    @continue
                @endif
                @php
                    $done = $index <= $currentIndex;
                    $current = $status === $order->status;
                @endphp
                <li class="{{ $compact ? 'flex items-center justify-between text-sm' : 'mb-6 last:mb-0' }}">
                    @unless ($compact)
                        <span class="absolute -left-1.5 flex h-3 w-3 items-center justify-center rounded-full {{ $done ? 'bg-brand-700' : 'bg-brand-200' }}"></span>
                    @endunless
                    <span class="{{ $done ? 'font-medium text-brand-900' : 'text-slate-400' }} {{ $current ? 'text-gold-700' : '' }}">
                        {{ \App\Models\Order::STATUS_LABELS[$status] }}
                    </span>
                    @if ($compact)
                        <span class="text-xs {{ $done ? 'text-green-700' : 'text-slate-400' }}">{{ $done ? '✓' : '—' }}</span>
                    @elseif ($current)
                        <span class="mt-0.5 block text-xs text-gold-700">Current status</span>
                    @endif
                </li>
            @endforeach
        </ol>
    @endif
</div>
