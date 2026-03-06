@component('emails.layout', [
    'subject' => $title,
    'emailTitle' => $title,
])
    <p style="margin:0 0 16px; font-size:14px; color:#334155; line-height:1.7;">
        {!! nl2br(e($body)) !!}
    </p>
    <p style="margin:0 0 20px;">
        <a href="{{ $actionUrl }}" class="email-btn" style="display:inline-block; padding:12px 28px; border-radius:999px; background:linear-gradient(135deg,#0891b2,#6366f1); color:#ffffff; text-decoration:none; font-size:14px; font-weight:600;">
            {{ $actionLabel }}
        </a>
    </p>
    <p style="margin:0; font-size:12px; color:#94a3b8;">
        {{ __('This is an automated email from :app.', ['app' => $appName]) }}
    </p>
@endcomponent
