@extends('mail.html.layout')

@section('content')
    <h1 style="margin:0 0 12px;font-family:Georgia,serif;font-size:22px;font-weight:600;color:#2f5c4a;">Verify your email</h1>
    <p style="margin:0 0 20px;">Hi {{ $userName }},</p>
    <p style="margin:0 0 20px;">Use the verification code below to complete your Shivibes account setup. This code expires in <strong>{{ $expiresMinutes }} minutes</strong>.</p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:24px 0;">
        <tr>
            <td align="center" style="background-color:#f4f9f6;border:2px dashed #9bc9b5;border-radius:12px;padding:24px;">
                <span style="font-family:'Courier New',monospace;font-size:32px;font-weight:700;letter-spacing:0.35em;color:#3a735c;">{{ $otp }}</span>
            </td>
        </tr>
    </table>

    <p style="margin:0;font-size:13px;color:#6aab8f;">If you did not request this code, you can safely ignore this email.</p>
@endsection
