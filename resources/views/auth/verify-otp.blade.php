<x-guest-layout>
    <div class="mb-4">
        <h1 class="text-lg font-semibold text-slate-900">{{ $title }}</h1>
        <p class="mt-2 text-sm text-slate-600">{{ $description }}</p>
        <p class="mt-1 text-xs text-slate-500">{{ __('Email: :email', ['email' => $email]) }}</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ $action }}" class="space-y-4">
        @csrf
        <div>
            <x-input-label for="code" :value="__('OTP code')" />
            <x-text-input id="code" class="block mt-1 w-full tracking-[0.35em]" type="text" name="code" :value="old('code')" required maxlength="6" autofocus />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center">
            {{ __('Verify OTP') }}
        </x-primary-button>
    </form>

    <form method="POST" action="{{ $resendAction }}" class="mt-3">
        @csrf
        <button type="submit" class="text-sm text-slate-600 hover:text-slate-900 underline">
            {{ __('Resend OTP') }}
        </button>
    </form>
</x-guest-layout>
