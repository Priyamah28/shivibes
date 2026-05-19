@extends('mail.html.layout')

@section('content')
    <h1 style="margin:0 0 12px;font-family:Georgia,serif;font-size:22px;font-weight:600;color:#2f5c4a;">We received your inquiry</h1>
    <p style="margin:0 0 20px;">Hi {{ $inquiry->name }},</p>
    <p style="margin:0 0 20px;">Thank you for reaching out to Shivibes for corporate or bulk gifting. Our team will review your request and get back to you within <strong>24–48 business hours</strong>.</p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 20px;background-color:#f4f9f6;border-radius:8px;">
        <tr><td style="padding:12px 16px;border-bottom:1px solid #e3f0ea;"><strong style="color:#3a735c;">Reference:</strong> Inquiry #{{ $inquiry->id }}</td></tr>
        @if ($inquiry->company_name)
            <tr><td style="padding:12px 16px;border-bottom:1px solid #e3f0ea;"><strong style="color:#3a735c;">Company:</strong> {{ $inquiry->company_name }}</td></tr>
        @endif
        @if ($inquiry->quantity)
            <tr><td style="padding:12px 16px;"><strong style="color:#3a735c;">Quantity:</strong> {{ $inquiry->quantity }}</td></tr>
        @endif
    </table>

    <p style="margin:0;font-size:13px;color:#6aab8f;">Need to add more details? Reply to this email or visit our corporate page.</p>

    @include('mail.html.partials.button', ['url' => $corporateUrl, 'label' => 'Corporate gifting'])
@endsection
