<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm text-slate-500">{{ __('Admin') }}</p>
                <h2 class="text-3xl font-semibold text-slate-900">{{ __('General Settings') }}</h2>
                <p class="text-sm text-slate-500 mt-1">{{ __('Configure system-wide settings.') }}</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">{{ __('Back to users') }}</a>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-3xl">
        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- AI Settings Card -->
        <div class="card-strong p-6">
            <h3 class="text-lg font-bold text-slate-900 mb-4">{{ __('AI Configuration') }}</h3>
            <form method="POST" action="{{ route('admin.settings.ai.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label for="gemini_model" class="text-sm font-medium text-slate-700">{{ __('Gemini model') }}</label>
                    <input
                        id="gemini_model"
                        type="text"
                        name="gemini_model"
                        value="{{ old('gemini_model', $geminiModel) }}"
                        class="mt-2 w-full rounded-xl border-slate-200"
                        placeholder="gemini-2.5-flash"
                        required
                    >
                    <p class="mt-1 text-xs text-slate-500">{{ __('Example: gemini-2.5-flash') }}</p>
                </div>

                <div>
                    <label for="gemini_api_key" class="text-sm font-medium text-slate-700">{{ __('Gemini API key') }}</label>
                    <input
                        id="gemini_api_key"
                        type="password"
                        name="gemini_api_key"
                        class="mt-2 w-full rounded-xl border-slate-200"
                        placeholder="AIza..."
                        autocomplete="off"
                    >
                    @if ($hasApiKey)
                        <p class="mt-1 text-xs text-slate-500">{{ __('Current key') }}: {{ $apiKeyMask }}</p>
                    @else
                        <p class="mt-1 text-xs text-slate-500">{{ __('No API key saved yet.') }}</p>
                    @endif
                </div>

                <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="clear_api_key" value="1" class="rounded border-slate-300 text-slate-900">
                    <span>{{ __('Clear stored API key') }}</span>
                </label>

                <div class="pt-2">
                    <button type="submit" class="btn-primary">{{ __('Save AI settings') }}</button>
                </div>
            </form>
        </div>

        <!-- Messenger Settings Card -->
        <div class="card-strong p-6">
            <h3 class="text-lg font-bold text-slate-900 mb-4">{{ __('Messenger Configuration') }}</h3>
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
                                {{ __('If enabled, users will only see project members in their contact list by default. Users can still search for others by email/name if they know it.') }}
                            </p>
                        </div>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-primary">{{ __('Save Messenger settings') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
