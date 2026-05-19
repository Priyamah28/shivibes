@extends('mail.html.layout')

@section('content')
    <h1 style="margin:0 0 12px;font-family:Georgia,serif;font-size:22px;font-weight:600;color:#2f5c4a;">New corporate inquiry</h1>
    <p style="margin:0 0 20px;">A new corporate / bulk gifting inquiry was submitted on the website.</p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 20px;background-color:#f4f9f6;border-radius:8px;">
        <tr><td style="padding:12px 16px;border-bottom:1px solid #e3f0ea;"><strong style="color:#3a735c;">Name:</strong> {{ $inquiry->name }}</td></tr>
        @if ($inquiry->company_name)
            <tr><td style="padding:12px 16px;border-bottom:1px solid #e3f0ea;"><strong style="color:#3a735c;">Company:</strong> {{ $inquiry->company_name }}</td></tr>
        @endif
        <tr><td style="padding:12px 16px;border-bottom:1px solid #e3f0ea;"><strong style="color:#3a735c;">Email:</strong> <a href="mailto:{{ $inquiry->email }}" style="color:#3a735c;">{{ $inquiry->email }}</a></td></tr>
        @if ($inquiry->phone)
            <tr><td style="padding:12px 16px;border-bottom:1px solid #e3f0ea;"><strong style="color:#3a735c;">Phone:</strong> {{ $inquiry->phone }}</td></tr>
        @endif
        @if ($inquiry->quantity)
            <tr><td style="padding:12px 16px;border-bottom:1px solid #e3f0ea;"><strong style="color:#3a735c;">Quantity:</strong> {{ $inquiry->quantity }}</td></tr>
        @endif
        <tr><td style="padding:12px 16px;"><strong style="color:#3a735c;">Message:</strong><br>{{ $inquiry->message }}</td></tr>
    </table>

    @include('mail.html.partials.button', ['url' => $adminUrl, 'label' => 'View in admin'])
@endsection
