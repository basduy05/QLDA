<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm text-slate-500">{{ __('Admin') }}</p>
                <h2 class="text-3xl font-semibold text-slate-900">{{ __('Messenger Settings') }}</h2>
                <p class="text-sm text-slate-500 mt-1">{{ __('Configure messaging permissions and restrictions.') }}</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">{{ __('Back to users') }}</a>
        </div>
    </x-slot>

    <div class="card-strong p-6 max-w-3xl">

        <form method="POST" action="{{ route('admin.settings.messenger.update') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="flex items-start gap-3">
                    <input
                        type="checkbox"
                        name="project_members_only"
                        value="1"
                        class="rounded border-slate-300 text-slate-900 mt-1"
                        {{ $projectMembersOnly ? 'checked' : '' }}
                    >
                    <div>
                        <span class="block text-sm font-medium text-slate-900">{{ __('Restrict to Project Members Only') }}</span>
                        <p class="text-sm text-slate-500 mt-1">
                            {{ __('If enabled, users will only see project members in their contact list by default. Users can still search for others by email if they know it.') }}
                        </p>
                    </div>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <button type="submit" class="btn-primary">{{ __('Save settings') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
