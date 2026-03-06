@component('emails.layout', [
    'subject' => __('Your verification code'),
    'emailTitle' => __('Verification Code'),
    'emailSubtitle' => __('Use this code to continue :purpose.', ['purpose' => $purposeLabel]),
])
    <p style="margin:0 0 12px; font-size:15px; color:#0f172a;">{{ __('Hello,') }}</p>
    <p style="margin:0 0 16px; font-size:14px; color:#334155;">
        {{ __('Use the OTP code below to continue :purpose.', ['purpose' => $purposeLabel]) }}
    </p>
    <div style="text-align:center; margin:24px 0;">
        <div style="display:inline-block; padding:16px 40px; background:linear-gradient(135deg,#f0fdfa,#eef2ff); border:2px dashed #0891b2; border-radius:16px;">
            <span style="font-size:32px; font-weight:700; letter-spacing:8px; color:#0891b2;">{{ $code }}</span>
        </div>
    </div>
    <p style="margin:0 0 8px; font-size:13px; color:#64748b;">{{ __('This code expires in 10 minutes.') }}</p>
    <p style="margin:0; font-size:13px; color:#94a3b8;">{{ __('If you did not request this, please ignore this email.') }}</p>
@endcomponent
