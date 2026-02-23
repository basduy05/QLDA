<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-900">{{ __('Profile') }}</h1>
    </x-slot>

    <div class="space-y-6 max-w-2xl">
        <div class="bg-white border border-slate-200 rounded-lg p-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="bg-white border border-slate-200 rounded-lg p-6">
            @include('profile.partials.update-password-form')
        </div>

        <div class="bg-white border border-slate-200 rounded-lg p-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
