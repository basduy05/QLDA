<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <div x-data="{ show: false }" class="relative mt-1">
                <x-text-input id="password" class="block w-full pr-14"
                        name="password"
                        x-bind:type="show ? 'text' : 'password'"
                        required autocomplete="new-password" />
                <button type="button" class="absolute inset-y-0 right-3 text-xs font-semibold text-slate-500" @click="show = !show" x-text="show ? '{{ __('Hide') }}' : '{{ __('Show') }}'"></button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <div x-data="{ show: false }" class="relative mt-1">
                <x-text-input id="password_confirmation" class="block w-full pr-14"
                        name="password_confirmation"
                        x-bind:type="show ? 'text' : 'password'"
                        required autocomplete="new-password" />
                <button type="button" class="absolute inset-y-0 right-3 text-xs font-semibold text-slate-500" @click="show = !show" x-text="show ? '{{ __('Hide') }}' : '{{ __('Show') }}'"></button>
            </div>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-4">
            <label for="accept_terms" class="inline-flex items-start gap-2 text-sm text-slate-600">
                <input id="accept_terms" type="checkbox" name="accept_terms" value="1" class="mt-0.5 rounded border-slate-300 text-slate-900 focus:ring-slate-400" @checked(old('accept_terms'))>
                <span>
                    {{ __('I agree to the') }}
                    <a href="{{ route('terms.show') }}" target="_blank" class="underline text-slate-800 hover:text-slate-900">{{ __('Terms of Use') }}</a>
                    {{ __('and understand project/messenger policies.') }}
                </span>
            </label>
            <x-input-error :messages="$errors->get('accept_terms')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
