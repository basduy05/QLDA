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

        <!-- Email Configuration Card -->
        <div class="card-strong p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-900">✉ {{ __('Email Configuration') }}</h3>
                <form method="POST" action="{{ route('admin.settings.email.test') }}">
                    @csrf
                    <button type="submit" class="btn-secondary text-sm">✉ {{ __('Send test email') }}</button>
                </form>
            </div>

            @if ($errors->has('email_test'))
                <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-3 text-sm text-red-700">
                    {{ $errors->first('email_test') }}
                </div>
            @endif

            <div class="mb-4 rounded-xl bg-amber-50 border border-amber-200 p-3 text-sm text-amber-800">
                <strong>{{ __('Note') }}:</strong> {{ __('Render free tier blocks SMTP (port 587/465). Use Brevo (300 emails/day free). Sign up at brevo.com → SMTP & API → API Keys.') }}
            </div>

            <form method="POST" action="{{ route('admin.settings.email.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label for="mail_provider" class="text-sm font-medium text-slate-700">{{ __('Email Provider') }}</label>
                    <select id="mail_provider" name="mail_provider" class="mt-2 w-full rounded-xl border-slate-200" onchange="toggleMailFields()">
                        <option value="brevo" {{ $mailProvider === 'brevo' ? 'selected' : '' }}>Brevo (API)</option>
                        <option value="smtp" {{ $mailProvider === 'smtp' ? 'selected' : '' }}>SMTP</option>
                    </select>
                </div>

                <div>
                    <label for="mail_from_address" class="text-sm font-medium text-slate-700">{{ __('Sender Email') }}</label>
                    <input id="mail_from_address" type="email" name="mail_from_address" value="{{ old('mail_from_address', $mailFromAddress) }}" class="mt-2 w-full rounded-xl border-slate-200" required>
                </div>

                {{-- Brevo fields --}}
                <div id="brevo-fields" class="space-y-4">
                    <div>
                        <label for="brevo_api_key" class="text-sm font-medium text-slate-700">{{ __('Brevo API Key') }}</label>
                        <div class="relative">
                            <input id="brevo_api_key" type="password" name="brevo_api_key" class="mt-2 w-full rounded-xl border-slate-200 pr-10" placeholder="xkeysib-..." autocomplete="off">
                            <button type="button" onclick="togglePassword('brevo_api_key')" class="absolute right-3 top-1/2 -translate-y-1/2 mt-1 text-slate-400 hover:text-slate-600">
                                👁
                            </button>
                        </div>
                        @if ($hasBrevoKey)
                            <p class="mt-1 text-xs text-slate-500">{{ __('Current key') }}: {{ $brevoKeyMask }}</p>
                        @else
                            <p class="mt-1 text-xs text-slate-500">{{ __('No API key saved yet.') }}</p>
                        @endif

                        <label class="inline-flex items-center gap-2 text-sm text-slate-600 mt-2">
                            <input type="checkbox" name="clear_brevo_key" value="1" class="rounded border-slate-300 text-slate-900">
                            <span>{{ __('Clear stored Brevo API key') }}</span>
                        </label>
                    </div>
                </div>

                {{-- SMTP fields --}}
                <div id="smtp-fields" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="smtp_host" class="text-sm font-medium text-slate-700">{{ __('SMTP Server') }}</label>
                            <input id="smtp_host" type="text" name="smtp_host" value="{{ old('smtp_host', $smtpHost) }}" class="mt-2 w-full rounded-xl border-slate-200" placeholder="smtp.gmail.com">
                        </div>
                        <div>
                            <label for="smtp_port" class="text-sm font-medium text-slate-700">{{ __('SMTP Port') }}</label>
                            <input id="smtp_port" type="number" name="smtp_port" value="{{ old('smtp_port', $smtpPort) }}" class="mt-2 w-full rounded-xl border-slate-200" placeholder="587">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="smtp_username" class="text-sm font-medium text-slate-700">{{ __('SMTP Username') }}</label>
                            <input id="smtp_username" type="text" name="smtp_username" value="{{ old('smtp_username', $smtpUsername) }}" class="mt-2 w-full rounded-xl border-slate-200">
                        </div>
                        <div>
                            <label for="smtp_password" class="text-sm font-medium text-slate-700">{{ __('SMTP App Password') }}</label>
                            <div class="relative">
                                <input id="smtp_password" type="password" name="smtp_password" class="mt-2 w-full rounded-xl border-slate-200 pr-10" autocomplete="off">
                                <button type="button" onclick="togglePassword('smtp_password')" class="absolute right-3 top-1/2 -translate-y-1/2 mt-1 text-slate-400 hover:text-slate-600">
                                    👁
                                </button>
                            </div>
                            @if ($hasSmtpPassword)
                                <p class="mt-1 text-xs text-slate-500">{{ __('Current') }}: {{ $smtpPasswordMask }}</p>
                            @endif

                            <label class="inline-flex items-center gap-2 text-sm text-slate-600 mt-2">
                                <input type="checkbox" name="clear_smtp_password" value="1" class="rounded border-slate-300 text-slate-900">
                                <span>{{ __('Clear stored SMTP password') }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-primary">{{ __('Save Email settings') }}</button>
                </div>
            </form>
        </div>

        <script>
            function toggleMailFields() {
                const provider = document.getElementById('mail_provider').value;
                document.getElementById('brevo-fields').style.display = provider === 'brevo' ? 'block' : 'none';
                document.getElementById('smtp-fields').style.display = provider === 'smtp' ? 'block' : 'none';
            }
            function togglePassword(id) {
                const el = document.getElementById(id);
                el.type = el.type === 'password' ? 'text' : 'password';
            }
            toggleMailFields();
        </script>
    </div>
</x-app-layout>
