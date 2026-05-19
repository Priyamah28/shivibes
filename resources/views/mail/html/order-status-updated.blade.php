@extends('mail.html.layout')

@section('content')
    <h1 style="margin:0 0 12px;font-family:Georgia,serif;font-size:22px;font-weight:600;color:#2f5c4a;">Order status updated</h1>
    <p style="margin:0 0 20px;">Your order <strong>{{ $order->order_number }}</strong> has been updated.</p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 20px;">
        <tr>
            <td style="padding:14px;background-color:#f4f9f6;border-radius:8px;text-align:center;">
                <p style="margin:0 0 4px;font-size:12px;color:#9bc9b5;text-decoration:line-through;">{{ $previousStatusLabel }}</p>
                <p style="margin:0;font-family:Georgia,serif;font-size:20px;font-weight:600;color:#3a735c;">{{ $newStatusLabel }}</p>
            </td>
        </tr>
    </table>

    @if ($hasTracking)
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 20px;background-color:#f4f9f6;border-radius:8px;">
            <tr>
                <td style="padding:16px;">
                    @if ($order->courier_partner)
                        <p style="margin:0 0 8px;font-size:13px;color:#6aab8f;"><strong style="color:#3a735c;">Courier:</strong> {{ $order->courier_partner }}</p>
                    @endif
                    <p style="margin:0 0 8px;font-size:13px;color:#6aab8f;"><strong style="color:#3a735c;">Tracking #:</strong> {{ $order->tracking_number }}</p>
                    @if ($order->tracking_url)
                        <p style="margin:0;font-size:13px;"><a href="{{ $order->tracking_url }}" style="color:#3a735c;font-weight:600;">Track your shipment</a></p>
                    @endif
                </td>
            </tr>
        </table>
    @endif

    @include('mail.html.partials.order-items', ['items' => $items])

    @include('mail.html.partials.button', ['url' => $orderUrl, 'label' => 'View order'])
@endsection
