<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <div x-data="{ show: false }" class="relative mt-1">
                <x-text-input id="password" class="block w-full pr-14" name="password" x-bind:type="show ? 'text' : 'password'" required autocomplete="new-password" />
                <button type="button" class="absolute inset-y-0 right-3 text-xs font-semibold text-slate-500" @click="show = !show" x-text="show ? '{{ __('Hide') }}' : '{{ __('Show') }}'"></button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <div x-data="{ show: false }" class="relative mt-1">
                <x-text-input id="password_confirmation" class="block w-full pr-14"
                        name="password_confirmation" x-bind:type="show ? 'text' : 'password'" required autocomplete="new-password" />
                <button type="button" class="absolute inset-y-0 right-3 text-xs font-semibold text-slate-500" @click="show = !show" x-text="show ? '{{ __('Hide') }}' : '{{ __('Show') }}'"></button>
            </div>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
