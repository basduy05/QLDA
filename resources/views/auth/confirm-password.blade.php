<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <div x-data="{ show: false }" class="relative mt-1">
                <x-text-input id="password" class="block w-full pr-14"
                        name="password"
                        x-bind:type="show ? 'text' : 'password'"
                        required autocomplete="current-password" />
                <button type="button" class="absolute inset-y-0 right-3 text-xs font-semibold text-slate-500" @click="show = !show" x-text="show ? '{{ __('Hide') }}' : '{{ __('Show') }}'"></button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
