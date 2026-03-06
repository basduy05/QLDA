<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm text-slate-500">{{ __('Admin') }}</p>
                <h2 class="text-3xl font-semibold text-slate-900">{{ __('Email Composer') }}</h2>
                <p class="text-sm text-slate-500 mt-1">{{ __('Compose and send professional emails to team members.') }}</p>
            </div>
            <a href="{{ route('admin.settings.ai.edit') }}" class="btn-secondary" wire:navigate>
                {{ __('Settings') }}
            </a>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->has('send'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            {!! nl2br(e($errors->first('send'))) !!}
        </div>
    @endif

    <div x-data="emailComposer()" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Template Picker --}}
        <div class="lg:col-span-1 space-y-4">
            {{-- Templates --}}
            <div class="card-strong p-5">
                <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                    {{ __('Templates') }}
                </h3>
                <div class="space-y-1.5 max-h-[60vh] overflow-y-auto pr-1">
                    @foreach ($templates as $tpl)
                        <button
                            type="button"
                            @click="applyTemplate('{{ $tpl['key'] }}')"
                            class="w-full text-left px-3 py-2.5 rounded-lg text-sm transition-all hover:bg-indigo-50 hover:text-indigo-700 border border-transparent hover:border-indigo-200 flex items-center gap-2.5"
                            :class="selectedTemplate === '{{ $tpl['key'] }}' ? 'bg-indigo-50 text-indigo-700 border-indigo-200 font-medium' : 'text-slate-600'"
                        >
                            <span class="text-base flex-shrink-0">{{ $tpl['icon'] }}</span>
                            <span class="truncate">{{ $tpl['name'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- AI Writing Assistant --}}
            <div class="card-strong p-5">
                <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    {{ __('AI Assistant') }}
                </h3>
                <textarea
                    x-model="aiPrompt"
                    rows="3"
                    class="w-full rounded-xl border-slate-200 text-sm placeholder:text-slate-400 resize-none"
                    placeholder="{{ __('Describe what you want to write...') }}"
                ></textarea>
                <p class="text-xs text-slate-400 mt-1 mb-3">{{ __('E.g. "Write a meeting invitation for tomorrow at 3pm about sprint review"') }}</p>
                <button
                    type="button"
                    @click="askAi()"
                    :disabled="aiLoading || !aiPrompt.trim()"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    style="background: linear-gradient(135deg, #0891b2, #6366f1, #7c3aed);"
                >
                    <template x-if="aiLoading">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                    </template>
                    <template x-if="!aiLoading">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </template>
                    <span x-text="aiLoading ? '{{ __('Generating...') }}' : '{{ __('Generate with AI') }}'"></span>
                </button>
                <template x-if="aiError">
                    <p class="mt-2 text-xs text-red-500" x-text="aiError"></p>
                </template>
            </div>
        </div>

        {{-- Right: Compose Area --}}
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('admin.email-composer.send') }}" class="card-strong overflow-hidden">
                @csrf

                {{-- Header Bar --}}
                <div class="px-5 py-3 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                    <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ __('New Message') }}
                    </h3>
                </div>

                {{-- Recipients --}}
                <div class="px-5 py-3 border-b border-slate-100" x-data="{ showDropdown: false, search: '' }">
                    <div class="flex items-start gap-3">
                        <label class="text-sm font-medium text-slate-500 pt-1.5 flex-shrink-0 w-10">{{ __('To') }}</label>
                        <div class="flex-1">
                            {{-- Selected Recipients --}}
                            <div class="flex flex-wrap gap-1.5 mb-1.5" x-show="recipients.length > 0">
                                <template x-for="(r, idx) in recipients" :key="idx">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 px-2.5 py-1 text-xs font-medium">
                                        <span x-text="r.name || r.email"></span>
                                        <input type="hidden" name="recipients[]" :value="r.email">
                                        <button type="button" @click="removeRecipient(idx)" class="ml-0.5 hover:text-red-500 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </span>
                                </template>
                            </div>
                            {{-- Search Input --}}
                            <div class="relative">
                                <input
                                    type="text"
                                    x-model="search"
                                    @focus="showDropdown = true"
                                    @click.away="showDropdown = false"
                                    @keydown.escape="showDropdown = false"
                                    class="w-full border-0 text-sm p-1.5 focus:ring-0 placeholder:text-slate-400"
                                    placeholder="{{ __('Search by name or type an email...') }}"
                                    @keydown.enter.prevent="handleEnterKey(search)"
                                >
                                {{-- Dropdown --}}
                                <div
                                    x-show="showDropdown && search.length > 0"
                                    x-transition
                                    class="absolute z-50 top-full left-0 right-0 mt-1 bg-white rounded-xl shadow-lg border border-slate-200 max-h-48 overflow-y-auto"
                                >
                                    @foreach ($users as $u)
                                        <button
                                            type="button"
                                            x-show="('{{ strtolower($u->name) }}' + '{{ strtolower($u->email) }}').includes(search.toLowerCase()) && !recipients.some(r => r.email === '{{ $u->email }}')"
                                            @click="addRecipient({ name: '{{ addslashes($u->name) }}', email: '{{ $u->email }}' }); search = ''; showDropdown = false;"
                                            class="w-full text-left px-4 py-2.5 text-sm hover:bg-indigo-50 transition-colors flex items-center gap-3"
                                        >
                                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-cyan-500 to-indigo-500 flex items-center justify-center flex-shrink-0">
                                                <span class="text-white text-xs font-bold">{{ strtoupper(mb_substr($u->name, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-900">{{ $u->name }}</div>
                                                <div class="text-xs text-slate-500">{{ $u->email }}</div>
                                            </div>
                                        </button>
                                    @endforeach
                                    {{-- Option to add typed email --}}
                                    <button
                                        type="button"
                                        x-show="search.includes('@') && !recipients.some(r => r.email === search)"
                                        @click="addRecipient({ name: '', email: search }); search = ''; showDropdown = false;"
                                        class="w-full text-left px-4 py-2.5 text-sm hover:bg-indigo-50 transition-colors flex items-center gap-2 border-t border-slate-100"
                                    >
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span>{{ __('Send to') }} <strong x-text="search"></strong></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        {{-- Send All Button --}}
                        <button
                            type="button"
                            @click="addAllUsers()"
                            class="text-xs text-indigo-600 hover:text-indigo-800 font-medium whitespace-nowrap pt-1.5 transition-colors"
                        >{{ __('All users') }}</button>
                    </div>
                    @error('recipients')
                        <p class="text-xs text-red-500 mt-1 pl-13">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Subject --}}
                <div class="px-5 py-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <label class="text-sm font-medium text-slate-500 flex-shrink-0 w-10">{{ __('Sub') }}</label>
                        <input
                            x-model="subject"
                            type="text"
                            name="subject"
                            value="{{ old('subject') }}"
                            class="flex-1 border-0 text-sm p-1.5 font-medium focus:ring-0 placeholder:text-slate-400"
                            placeholder="{{ __('Email subject...') }}"
                            required
                        >
                    </div>
                    @error('subject')
                        <p class="text-xs text-red-500 mt-1 pl-13">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Body --}}
                <div class="px-5 pt-4 pb-2">
                    {{-- Formatting Toolbar --}}
                    <div class="flex items-center gap-1 mb-3 pb-2 border-b border-slate-100">
                        <button type="button" @click="insertFormat('**', '**')" class="p-1.5 rounded hover:bg-slate-100 text-slate-500 hover:text-slate-700 transition-colors" title="Bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6z M6 12h9a4 4 0 014 4 4 4 0 01-4 4H6z"/></svg>
                        </button>
                        <button type="button" @click="insertFormat('*', '*')" class="p-1.5 rounded hover:bg-slate-100 text-slate-500 hover:text-slate-700 transition-colors" title="Italic">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 4h4m-2 0v16m-4 0h8"/></svg>
                        </button>
                        <button type="button" @click="insertText('\n- ')" class="p-1.5 rounded hover:bg-slate-100 text-slate-500 hover:text-slate-700 transition-colors" title="List">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        </button>
                        <button type="button" @click="insertText('\n## ')" class="p-1.5 rounded hover:bg-slate-100 text-slate-500 hover:text-slate-700 transition-colors" title="Heading">
                            <span class="text-xs font-bold">H</span>
                        </button>
                        <div class="h-4 w-px bg-slate-200 mx-1"></div>
                        <span class="text-xs text-slate-400">{{ __('Supports Markdown') }}</span>
                    </div>

                    <textarea
                        x-ref="bodyEditor"
                        x-model="body"
                        name="body"
                        rows="14"
                        class="w-full border-0 text-sm p-0 focus:ring-0 resize-none placeholder:text-slate-400 leading-relaxed"
                        placeholder="{{ __("Write your email content here...\n\nYou can use **bold**, *italic*, and - bullet lists") }}"
                        required
                    >{{ old('body') }}</textarea>
                    @error('body')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Footer Actions --}}
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <button
                            type="submit"
                            :disabled="!canSend()"
                            class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white transition-all disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-lg"
                            style="background: linear-gradient(135deg, #0891b2, #6366f1);"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            {{ __('Send') }}
                        </button>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-slate-400">
                        <span x-show="recipients.length > 0" x-text="recipients.length + ' {{ __('recipient(s)') }}'"></span>
                        <span x-text="body.length + ' {{ __('chars') }}'"></span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    function emailComposer() {
        return {
            recipients: [],
            subject: '{{ old('subject', '') }}',
            body: `{{ old('body', '') }}`,
            selectedTemplate: null,
            aiPrompt: '',
            aiLoading: false,
            aiError: null,
            allUsers: @json($users),

            canSend() {
                return this.recipients.length > 0 && this.subject.trim() && this.body.trim();
            },

            addRecipient(r) {
                if (!this.recipients.some(x => x.email === r.email)) {
                    this.recipients.push(r);
                }
            },

            removeRecipient(idx) {
                this.recipients.splice(idx, 1);
            },

            addAllUsers() {
                this.allUsers.forEach(u => {
                    if (!this.recipients.some(r => r.email === u.email)) {
                        this.recipients.push({ name: u.name, email: u.email });
                    }
                });
            },

            handleEnterKey(val) {
                if (val.includes('@')) {
                    this.addRecipient({ name: '', email: val });
                }
            },

            applyTemplate(key) {
                this.selectedTemplate = key;
                fetch(`{{ route('admin.email-composer.template') }}?key=${key}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.subject) this.subject = data.subject;
                        if (data.body) this.body = data.body;
                    });
            },

            insertFormat(before, after) {
                const el = this.$refs.bodyEditor;
                const start = el.selectionStart;
                const end = el.selectionEnd;
                const selected = this.body.substring(start, end) || '{{ __('text') }}';
                const replacement = before + selected + after;
                this.body = this.body.substring(0, start) + replacement + this.body.substring(end);
                this.$nextTick(() => {
                    el.focus();
                    el.setSelectionRange(start + before.length, start + before.length + selected.length);
                });
            },

            insertText(text) {
                const el = this.$refs.bodyEditor;
                const pos = el.selectionStart;
                this.body = this.body.substring(0, pos) + text + this.body.substring(pos);
                this.$nextTick(() => {
                    el.focus();
                    el.setSelectionRange(pos + text.length, pos + text.length);
                });
            },

            askAi() {
                if (!this.aiPrompt.trim() || this.aiLoading) return;
                this.aiLoading = true;
                this.aiError = null;

                fetch('{{ route('admin.email-composer.ai-suggest') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        prompt: this.aiPrompt,
                        context: this.body ? `Current draft:\n${this.body}` : null,
                    }),
                })
                .then(r => r.json())
                .then(data => {
                    if (data.error) {
                        this.aiError = data.error;
                    } else if (data.suggestion) {
                        this.body = data.suggestion;
                        this.aiPrompt = '';
                    }
                })
                .catch(err => {
                    this.aiError = '{{ __('Network error. Please try again.') }}';
                })
                .finally(() => {
                    this.aiLoading = false;
                });
            },
        };
    }
    </script>
    @endpush
</x-app-layout>
