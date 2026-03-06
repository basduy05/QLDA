{{-- Shared Aperlex email layout - used by all email templates --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>{{ $subject ?? config('app.name', 'Aperlex') }}</title>
    <!--[if mso]><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]-->
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; width: 100%; -webkit-text-size-adjust: none; }
        table { border-collapse: collapse; }
        img { border: 0; max-width: 100%; }
        a { color: #0891b2; text-decoration: none; }
        .email-btn { display: inline-block; padding: 12px 28px; border-radius: 999px; background: linear-gradient(135deg, #0891b2, #6366f1); color: #ffffff !important; text-decoration: none; font-size: 14px; font-weight: 600; }
        .email-btn-secondary { display: inline-block; padding: 10px 24px; border-radius: 999px; border: 2px solid #e2e8f0; color: #334155 !important; text-decoration: none; font-size: 13px; font-weight: 600; }
        @media only screen and (max-width: 620px) {
            .email-container { width: 100% !important; }
            .email-padding { padding: 20px 16px !important; }
        }
    </style>
</head>
<body style="margin:0; padding:0; background-color:#f1f5f9; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif; color:#0f172a; line-height:1.6;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f1f5f9;">
        {{-- Top spacer --}}
        <tr><td style="height:32px;"></td></tr>
        <tr>
            <td align="center" style="padding:0 16px;">
                <table role="presentation" class="email-container" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.08),0 4px 12px rgba(0,0,0,0.04);">

                    {{-- Logo header --}}
                    <tr>
                        <td style="padding:24px 32px; background:linear-gradient(135deg, #0891b2 0%, #6366f1 60%, #7c3aed 100%);">
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td>
                                        {{-- Inline SVG Aperlex icon --}}
                                        <table role="presentation" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="vertical-align:middle; padding-right:10px;">
                                                    <img src="{{ rtrim(config('app.url', ''), '/') }}/images/logo-icon.svg" alt="" width="32" height="32" style="display:block; width:32px; height:32px;">
                                                </td>
                                                <td style="vertical-align:middle;">
                                                    <span style="font-size:20px; font-weight:700; color:#ffffff; letter-spacing:-0.3px;">Aperlex</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Title section --}}
                    @if(!empty($emailTitle))
                    <tr>
                        <td class="email-padding" style="padding:28px 32px 0;">
                            <h1 style="margin:0; font-size:22px; font-weight:700; color:#0f172a; line-height:1.3;">{{ $emailTitle }}</h1>
                            @if(!empty($emailSubtitle))
                            <p style="margin:8px 0 0; font-size:14px; color:#64748b;">{{ $emailSubtitle }}</p>
                            @endif
                        </td>
                    </tr>
                    @endif

                    {{-- Content --}}
                    <tr>
                        <td class="email-padding" style="padding:20px 32px 28px;">
                            {{ $slot }}
                        </td>
                    </tr>

                    {{-- Divider --}}
                    <tr>
                        <td style="padding:0 32px;">
                            <div style="height:1px; background-color:#e2e8f0;"></div>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:20px 32px 24px; text-align:center;">
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <img src="{{ rtrim(config('app.url', ''), '/') }}/images/logo-icon.svg" alt="" width="20" height="20" style="display:inline-block; width:20px; height:20px; vertical-align:middle; opacity:0.5;">
                                        <span style="font-size:12px; color:#94a3b8; vertical-align:middle; margin-left:6px;">{{ config('app.name', 'Aperlex') }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-top:8px;">
                                        <p style="margin:0; font-size:11px; color:#cbd5e1;">
                                            © {{ date('Y') }} Aperlex. {{ __('All rights reserved.') }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        {{-- Bottom spacer --}}
        <tr><td style="height:32px;"></td></tr>
    </table>
</body>
</html>
