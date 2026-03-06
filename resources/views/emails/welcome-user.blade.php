@component('emails.layout', [
    'subject' => __('Welcome to :app', ['app' => $appName]),
    'emailTitle' => __('Welcome to :app', ['app' => $appName]),
    'emailSubtitle' => __('Your account has been created successfully.'),
])
    <p style="margin:0 0 12px; font-size:15px; color:#0f172a;">
        {{ __('Hello :name,', ['name' => $user->name]) }}
    </p>
    <p style="margin:0 0 12px; font-size:14px; color:#334155; line-height:1.7;">
        {{ __('Thanks for joining :app. You can now start managing projects, tasks, and team communication from your dashboard.', ['app' => $appName]) }}
    </p>
    <p style="margin:0 0 24px; font-size:14px; color:#334155; line-height:1.7;">
        {{ __('If this account was not created by you, please contact support immediately.') }}
    </p>
    <p style="margin:0 0 16px;">
        <a href="{{ $dashboardUrl }}" class="email-btn" style="display:inline-block; padding:12px 28px; border-radius:999px; background:linear-gradient(135deg,#0891b2,#6366f1); color:#ffffff; text-decoration:none; font-size:14px; font-weight:600;">
            {{ __('Open dashboard') }}
        </a>
    </p>
    <p style="margin:0; font-size:12px; color:#94a3b8;">
        {{ __('This is an automated email from :app.', ['app' => $appName]) }}
    </p>
@endcomponent
