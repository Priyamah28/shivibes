@extends('mail.html.layout')

@section('content')
    <h1 style="margin:0 0 12px;font-family:Georgia,serif;font-size:22px;font-weight:600;color:#2f5c4a;">{{ $title }}</h1>
    <p style="margin:0 0 20px;">{{ $bodyMessage }}</p>

    @if (! empty($details))
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 20px;background-color:#f4f9f6;border-radius:8px;">
            @foreach ($details as $label => $value)
                <tr>
                    <td style="padding:10px 16px;border-bottom:1px solid #e3f0ea;font-size:14px;">
                        <strong style="color:#3a735c;">{{ ucfirst(str_replace('_', ' ', $label)) }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

    @if ($actionUrl && $actionLabel)
        @include('mail.html.partials.button', ['url' => $actionUrl, 'label' => $actionLabel])
    @endif
@endsection
