<x-guest-layout>
    <div class="space-y-4">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">{{ __('Terms of Use') }}</h1>
            <p class="mt-2 text-sm text-slate-600">{{ __('These terms apply to project and team communication features in QhorizonPM.') }}</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 text-sm text-slate-700 space-y-3">
            <p>{{ __('1) Account Responsibility: You are responsible for your account activity and password security.') }}</p>
            <p>{{ __('2) Project Data: Tasks, comments, and files must be work-related and lawful.') }}</p>
            <p>{{ __('3) Messenger Usage: Do not send abuse, spam, credentials, OTPs, or sensitive private data.') }}</p>
            <p>{{ __('4) Group Changes: Group rename and nickname changes are visible to members via system notifications.') }}</p>
            <p>{{ __('5) Message Retention: Chat messages may be auto-cleaned by system policy (for example 24h windows).') }}</p>
            <p>{{ __('6) Admin Moderation: Admins may review logs and moderate user access to ensure service safety.') }}</p>
            <p>{{ __('7) Security Notice: OTP and password reset emails are security events and should be kept private.') }}</p>
        </div>

        <div class="text-sm">
            <a href="{{ route('register') }}" class="text-slate-700 underline hover:text-slate-900">{{ __('Back to Register') }}</a>
        </div>
    </div>
</x-guest-layout>
