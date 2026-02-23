<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ __('AI Settings') }}</h1>
                <p class="text-sm text-slate-500 mt-1">{{ __('Configure Google AI Studio Gemini API') }}</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="px-3 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">
                {{ __('Back to users') }}
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white border border-slate-200 rounded-lg p-6">
            @if (session('status'))
                <div class="p-4 mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 mb-4 text-sm text-rose-700 bg-rose-50 border border-rose-200 rounded-lg">
                    <ul class="list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.settings.ai.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label for="gemini_model" class="block text-sm font-medium text-slate-700 mb-1">
                        {{ __('Gemini model') }}
                    </label>
                    <input
                        id="gemini_model"
                        type="text"
                        name="gemini_model"
                        value="{{ old('gemini_model', $geminiModel) }}"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="gemini-3.0-flash"
                        required
                    >
                    <p class="mt-1 text-xs text-slate-500">{{ __('Example: gemini-3.0-flash') }}</p>
                </div>

                <div>
                    <label for="gemini_api_key" class="block text-sm font-medium text-slate-700 mb-1">
                        {{ __('Gemini API key') }}
                    </label>
                    <input
                        id="gemini_api_key"
                        type="password"
                        name="gemini_api_key"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="AIza..."
                        autocomplete="off"
                    >
                    @if ($hasApiKey)
                        <p class="mt-1 text-xs text-slate-500">{{ __('Current key') }}: {{ $apiKeyMask }}</p>
                    @else
                        <p class="mt-1 text-xs text-slate-500">{{ __('No API key saved yet.') }}</p>
                    @endif
                </div>

                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="clear_api_key" value="1" class="rounded border-slate-300">
                    <span>{{ __('Clear stored API key') }}</span>
                </label>

                <div class="pt-4 border-t border-slate-200">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        {{ __('Save settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
