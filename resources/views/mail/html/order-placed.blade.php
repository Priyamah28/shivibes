@extends('mail.html.layout')

@section('content')
    <h1 style="margin:0 0 12px;font-family:Georgia,serif;font-size:22px;font-weight:600;color:#2f5c4a;">Thank you for your order</h1>
    <p style="margin:0 0 20px;">Your order <strong>{{ $order->order_number }}</strong> has been placed successfully.</p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 20px;background-color:#f4f9f6;border-radius:8px;">
        <tr>
            <td style="padding:16px;">
                <p style="margin:0 0 8px;font-size:13px;color:#6aab8f;"><strong style="color:#3a735c;">Delivery:</strong> {{ $deliveryLabel }}</p>
                <p style="margin:0 0 8px;font-size:13px;color:#6aab8f;"><strong style="color:#3a735c;">Total:</strong> ₹{{ number_format($order->total_amount, 2) }}</p>
                <p style="margin:0;font-size:13px;color:#6aab8f;"><strong style="color:#3a735c;">Tracking:</strong> {{ $trackingPlaceholder }}</p>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 8px;font-size:13px;font-weight:600;color:#3a735c;text-transform:uppercase;letter-spacing:0.06em;">Order items</p>
    @include('mail.html.partials.order-items', ['items' => $items])

    <p style="margin:16px 0 8px;font-size:13px;font-weight:600;color:#3a735c;text-transform:uppercase;letter-spacing:0.06em;">Shipping address</p>
    <p style="margin:0 0 20px;font-size:14px;color:#274a3d;white-space:pre-line;background-color:#f4f9f6;padding:14px;border-radius:8px;">{{ $order->shippingFormatted() }}</p>

    @include('mail.html.partials.button', ['url' => $orderUrl, 'label' => 'View order details'])
@endsection
