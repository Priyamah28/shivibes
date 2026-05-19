<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $title ?? config('app.name') }}</title>
    <!--[if mso]><style type="text/css">body,table,td{font-family:Georgia,serif!important;}</style><![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#f4f9f6;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;-webkit-font-smoothing:antialiased;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f4f9f6;">
    <tr>
        <td align="center" style="padding:32px 16px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(33,61,51,0.08);">
                {{-- Header --}}
                <tr>
                    <td style="background:linear-gradient(135deg,#3a735c 0%,#2f5c4a 100%);padding:28px 32px;text-align:center;">
                        <p style="margin:0;font-family:Georgia,'Times New Roman',serif;font-size:28px;font-weight:600;color:#ffffff;letter-spacing:0.04em;">Shivibes</p>
                        <p style="margin:8px 0 0;font-size:12px;color:#c5e0d4;letter-spacing:0.12em;text-transform:uppercase;">Herbal Skincare &amp; Gifting</p>
                    </td>
                </tr>
                {{-- Body --}}
                <tr>
                    <td style="padding:32px 32px 24px;color:#274a3d;font-size:15px;line-height:1.65;">
                        @yield('content')
                    </td>
                </tr>
                {{-- Footer --}}
                <tr>
                    <td style="background-color:#f4f9f6;padding:24px 32px;border-top:1px solid #e3f0ea;text-align:center;">
                        <p style="margin:0 0 8px;font-size:13px;color:#6aab8f;">
                            Questions? Email us at
                            <a href="mailto:{{ config('shivibes.mail.support_address') }}" style="color:#3a735c;text-decoration:none;font-weight:600;">{{ config('shivibes.mail.support_address') }}</a>
                        </p>
                        <p style="margin:0;font-size:11px;color:#9bc9b5;">
                            &copy; {{ date('Y') }} Shivibes. All rights reserved.
                        </p>
                        <p style="margin:12px 0 0;font-size:11px;color:#9bc9b5;">
                            <a href="{{ config('app.url') }}" style="color:#4a9074;text-decoration:none;">Visit our store</a>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
