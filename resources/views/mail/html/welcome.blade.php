@extends('mail.html.layout')

@section('content')
    <h1 style="margin:0 0 12px;font-family:Georgia,serif;font-size:22px;font-weight:600;color:#2f5c4a;">Welcome to Shivibes</h1>
    <p style="margin:0 0 20px;">Hi {{ $userName }},</p>
    <p style="margin:0 0 20px;">Your email is verified and your account is ready. Explore our herbal skincare, personal gifting, corporate hampers, and festival collections — crafted with natural ingredients and care.</p>

    @include('mail.html.partials.button', ['url' => $shopUrl, 'label' => 'Start shopping'])

    <p style="margin:0;font-size:13px;color:#6aab8f;">We are glad to have you with us on this journey toward mindful, chemical-free beauty.</p>
@endsection
