<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm text-slate-500">{{ __('Account') }}</p>
                <h2 class="text-2xl font-semibold text-slate-900">{{ __('Profile') }}</h2>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-3xl">
        <div class="card-strong p-5">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="card-strong p-5">
            @include('profile.partials.update-password-form')
        </div>

        <div class="card-strong p-5">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
