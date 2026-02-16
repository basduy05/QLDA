<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm text-slate-500">{{ __('Messenger Terms') }}</p>
            <h2 class="text-2xl font-semibold text-slate-900">{{ __('Please review before using Messenger') }}</h2>
        </div>
    </x-slot>

    <div class="card-strong p-6 space-y-4">
        <p class="text-sm text-slate-700">{{ __('By continuing, you agree to use Messenger respectfully, avoid sharing sensitive personal data, and follow team communication rules.') }}</p>
        <ul class="list-disc pl-5 text-sm text-slate-600 space-y-1">
            <li>{{ __('Do not share passwords, OTPs, or private credentials in chat.') }}</li>
            <li>{{ __('Messages may be retained for auditing based on system policy.') }}</li>
            <li>{{ __('Admins can moderate content when needed.') }}</li>
        </ul>

        <div class="flex items-center gap-2 pt-2">
            <form method="POST" action="{{ route('messenger.terms.accept') }}">
                @csrf
                <button type="submit" class="btn-primary">{{ __('I agree and continue') }}</button>
            </form>

            <form method="POST" action="{{ route('messenger.terms.decline') }}">
                @csrf
                <button type="submit" class="btn-secondary">{{ __('Decline and go back') }}</button>
            </form>
        </div>
    </div>
</x-app-layout>
