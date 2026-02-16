<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #0f172a;">
    <p>{{ __('Hello,') }}</p>
    <p>{{ __('Use the OTP code below to continue :purpose.', ['purpose' => $purposeLabel]) }}</p>
    <p style="font-size: 24px; font-weight: 700; letter-spacing: 6px; margin: 16px 0;">{{ $code }}</p>
    <p>{{ __('This code expires in 10 minutes.') }}</p>
    <p>{{ __('If you did not request this, please ignore this email.') }}</p>
</div>
