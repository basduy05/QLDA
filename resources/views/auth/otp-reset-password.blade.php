<x-guest-layout>
    <div class="mb-4">
        <h1 class="text-lg font-semibold text-slate-900">{{ __('Set a new password') }}</h1>
        <p class="mt-2 text-sm text-slate-600">{{ __('OTP verified for :email. Enter your new password below.', ['email' => $email]) }}</p>
    </div>

    <form method="POST" action="{{ route('password.otp.reset.store') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="password" :value="__('New Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
        </div>

        <x-primary-button class="w-full justify-center">
            {{ __('Reset Password') }}
        </x-primary-button>
    </form>
</x-guest-layout>
