<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Welcome to :app', ['app' => $appName]) }}</title>
</head>
<body style="margin:0; padding:0; background:#f8fafc; font-family:Arial, Helvetica, sans-serif; color:#0f172a;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#f8fafc; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:620px; background:#ffffff; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden;">
                    <tr>
                        <td style="padding:22px 24px; background:linear-gradient(120deg, #0f766e, #0891b2); color:#ffffff;">
                            <h1 style="margin:0; font-size:22px; font-weight:700;">{{ __('Welcome to :app', ['app' => $appName]) }}</h1>
                            <p style="margin:8px 0 0; font-size:13px; opacity:.95;">{{ __('Your account has been created successfully.') }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 10px; font-size:15px;">{{ __('Hello :name,', ['name' => $user->name]) }}</p>
                            <p style="margin:0 0 10px; line-height:1.6; font-size:14px; color:#334155;">
                                {{ __('Thanks for joining :app. You can now start managing projects, tasks, and team communication from your dashboard.', ['app' => $appName]) }}
                            </p>
                            <p style="margin:0 0 18px; line-height:1.6; font-size:14px; color:#334155;">
                                {{ __('If this account was not created by you, please contact support immediately.') }}
                            </p>

                            <p style="margin:0 0 22px;">
                                <a href="{{ $dashboardUrl }}" style="display:inline-block; padding:11px 18px; border-radius:999px; background:#0f766e; color:#ffffff; text-decoration:none; font-size:14px; font-weight:600;">
                                    {{ __('Open dashboard') }}
                                </a>
                            </p>

                            <p style="margin:0; font-size:12px; color:#64748b;">
                                {{ __('This is an automated email from :app.', ['app' => $appName]) }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
