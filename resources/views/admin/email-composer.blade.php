<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 w-full">
            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ __('Email Composer') }}</h2>
                <p class="text-sm text-slate-500 mt-0.5">
                    {{ __('Compose professional emails with templates and AI assistance.') }}</p>
            </div>
            <a href="{{ route('admin.settings.ai.edit') }}" class="btn-secondary" wire:navigate>{{ __('Settings') }}</a>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
            {{ session('status') }}</div>
    @endif
    @if ($errors->has('send'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            {!! nl2br(e($errors->first('send'))) !!}</div>
    @endif

    <div x-data="emailComposer()" class="space-y-5">
        {{-- Variable Chips Bar --}}
        <div class="card-strong p-4">
            <div class="flex flex-wrap items-center gap-2">
                <span
                    class="text-xs font-bold text-slate-500 uppercase tracking-wider mr-1">{{ __('Variables') }}:</span>
                <template x-for="v in variables" :key="v.key">
                    <button type="button" @click="insertVariable(v.key)"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all hover:-translate-y-0.5"
                        style="background: var(--accent-soft); color: var(--accent); border: 1px solid rgba(99,102,241,0.15);">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                        <span x-text="v.key"></span>
                        <span class="text-indigo-400 font-normal" x-text="'→ ' + v.preview"></span>
                    </button>
                </template>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-5 gap-5">
            {{-- Left: Templates + AI --}}
            <div class="xl:col-span-1 space-y-4">
                {{-- Template Cards --}}
                <div class="card-strong p-4">
                    <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" style="color: var(--accent);" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                        </svg>
                        {{ __('Templates') }}
                    </h3>
                    <div class="space-y-1.5 max-h-[55vh] overflow-y-auto pr-1">
                        @foreach ($templates as $tpl)
                            <button type="button" @click="applyTemplate('{{ $tpl['key'] }}')"
                                class="w-full text-left px-3 py-2.5 rounded-xl text-sm transition-all hover:bg-indigo-50 hover:text-indigo-700 border border-transparent hover:border-indigo-200 group"
                                :class="selectedTemplate === '{{ $tpl['key'] }}' ? 'bg-indigo-50 text-indigo-700 border-indigo-200 font-medium' : 'text-slate-600'">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-base shrink-0">{{ $tpl['icon'] }}</span>
                                    <div class="min-w-0">
                                        <span class="block font-semibold text-sm truncate">{{ $tpl['name'] }}</span>
                                        <span
                                            class="block text-[11px] text-slate-400 group-hover:text-indigo-400 truncate mt-0.5">{{ $tpl['description'] ?? '' }}</span>
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Project/Task Selector --}}
                <div class="card-strong p-4">
                    <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z" />
                        </svg>
                        {{ __('Quick Insert') }}
                    </h3>
                    <select x-model="selectedProject" @change="onProjectChange()"
                        class="w-full text-sm rounded-xl border-slate-200 mb-2">
                        <option value="">{{ __('Select a project...') }}</option>
                        @foreach ($projects as $p)
                            <option value="{{ $p->name }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400">
                        {{ __('Selected project/task names will auto-fill in your email body.') }}</p>
                </div>

                {{-- AI Assistant --}}
                <div class="card-strong p-4">
                    <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        {{ __('AI Assistant') }}
                    </h3>
                    <textarea x-model="aiPrompt" rows="3"
                        class="w-full rounded-xl border-slate-200 text-sm placeholder:text-slate-400 resize-none"
                        placeholder="{{ __('Describe what you want to write...') }}"></textarea>
                    <p class="text-[11px] text-slate-400 mt-1 mb-3">
                        {{ __('E.g. "Write a meeting invitation for tomorrow at 3pm"') }}</p>
                    <button type="button" @click="askAi()" :disabled="aiLoading || !aiPrompt.trim()"
                        class="w-full btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                        <template x-if="aiLoading">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                            </svg>
                        </template>
                        <template x-if="!aiLoading">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </template>
                        <span x-text="aiLoading ? '{{ __('Generating...') }}' : '{{ __('Generate with AI') }}'"></span>
                    </button>
                    <template x-if="aiError">
                        <p class="mt-2 text-xs text-red-500" x-text="aiError"></p>
                    </template>
                </div>
            </div>

            {{-- Center: Compose + Preview --}}
            <div class="xl:col-span-4">
                <form method="POST" action="{{ route('admin.email-composer.send') }}"
                    class="card-strong overflow-hidden">
                    @csrf

                    {{-- Tabs: Write / Preview --}}
                    <div class="px-5 py-2.5 border-b border-slate-100 bg-slate-50/30 flex items-center justify-between">
                        <div class="flex items-center gap-1 rounded-lg bg-slate-100/80 p-0.5">
                            <button type="button" @click="activeTab = 'write'"
                                class="px-4 py-1.5 text-xs font-bold rounded-md transition-all"
                                :class="activeTab === 'write' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                    {{ __('Write') }}
                                </span>
                            </button>
                            <button type="button" @click="activeTab = 'preview'; refreshPreview()"
                                class="px-4 py-1.5 text-xs font-bold rounded-md transition-all"
                                :class="activeTab === 'preview' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    {{ __('Preview') }}
                                </span>
                            </button>
                        </div>
                        <div class="flex items-center gap-3 text-xs text-slate-400">
                            <span x-show="recipients.length > 0"
                                x-text="recipients.length + ' {{ __('recipient(s)') }}'"></span>
                            <span x-text="body.length + ' {{ __('chars') }}'"></span>
                        </div>
                    </div>

                    {{-- Write Tab --}}
                    <div x-show="activeTab === 'write'">
                        {{-- Recipients --}}
                        <div class="px-5 py-3 border-b border-slate-100" x-data="{ showDropdown: false, search: '' }">
                            <div class="flex items-start gap-3">
                                <label
                                    class="text-sm font-medium text-slate-500 pt-1.5 shrink-0 w-12">{{ __('To') }}</label>
                                <div class="flex-1">
                                    <div class="flex flex-wrap gap-1.5 mb-1.5" x-show="recipients.length > 0">
                                        <template x-for="(r, idx) in recipients" :key="idx">
                                            <span
                                                class="inline-flex items-center gap-1 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-700 px-2.5 py-1 text-xs font-medium animate-scale-in">
                                                <span x-text="r.name || r.email"></span>
                                                <input type="hidden" name="recipients[]" :value="r.email">
                                                <button type="button" @click="removeRecipient(idx)"
                                                    class="ml-0.5 hover:text-red-500 transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>
                                    </div>
                                    <div class="relative">
                                        <input type="text" x-model="search" @focus="showDropdown = true"
                                            @click.away="showDropdown = false" @keydown.escape="showDropdown = false"
                                            class="w-full border-0 text-sm p-1.5 focus:ring-0 placeholder:text-slate-400"
                                            placeholder="{{ __('Search by name or type an email...') }}"
                                            @keydown.enter.prevent="handleEnterKey(search)">
                                        <div x-show="showDropdown && search.length > 0" x-transition
                                            class="absolute z-50 top-full left-0 right-0 mt-1 bg-white rounded-xl border border-slate-200 max-h-48 overflow-y-auto"
                                            style="box-shadow: var(--shadow-lg);">
                                            @foreach ($users as $u)
                                                <button type="button"
                                                    x-show="('{{ strtolower($u->name) }}' + '{{ strtolower($u->email) }}').includes(search.toLowerCase()) && !recipients.some(r => r.email === '{{ $u->email }}')"
                                                    @click="addRecipient({ name: '{{ addslashes($u->name) }}', email: '{{ $u->email }}' }); search = ''; showDropdown = false;"
                                                    class="w-full text-left px-4 py-2.5 text-sm hover:bg-indigo-50 transition-colors flex items-center gap-3">
                                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0"
                                                        style="background: var(--gradient);">
                                                        <span
                                                            class="text-white text-xs font-bold">{{ strtoupper(mb_substr($u->name, 0, 1)) }}</span>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-slate-900">{{ $u->name }}</div>
                                                        <div class="text-xs text-slate-500">{{ $u->email }}</div>
                                                    </div>
                                                </button>
                                            @endforeach
                                            <button type="button"
                                                x-show="search.includes('@') && !recipients.some(r => r.email === search)"
                                                @click="addRecipient({ name: '', email: search }); search = ''; showDropdown = false;"
                                                class="w-full text-left px-4 py-2.5 text-sm hover:bg-indigo-50 transition-colors flex items-center gap-2 border-t border-slate-100">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                <span>{{ __('Send to') }} <strong x-text="search"></strong></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" @click="addAllUsers()"
                                    class="text-xs font-medium whitespace-nowrap pt-1.5 transition-colors hover:text-indigo-800"
                                    style="color: var(--accent);">{{ __('All users') }}</button>
                            </div>
                            @error('recipients')
                                <p class="text-xs text-red-500 mt-1 pl-12">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Subject --}}
                        <div class="px-5 py-3 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <label
                                    class="text-sm font-medium text-slate-500 shrink-0 w-12">{{ __('Subject') }}</label>
                                <input x-model="subject" type="text" name="subject" value="{{ old('subject') }}"
                                    class="flex-1 border-0 text-sm p-1.5 font-medium focus:ring-0 placeholder:text-slate-400"
                                    placeholder="{{ __('Email subject...') }}" required>
                            </div>
                            @error('subject') <p class="text-xs text-red-500 mt-1 pl-12">{{ $message }}</p> @enderror
                        </div>

                        {{-- Body --}}
                        <div class="px-5 pt-4 pb-2">
                            <div class="flex items-center gap-1 mb-3 pb-2 border-b border-slate-100">
                                <button type="button" @click="insertFormat('**', '**')"
                                    class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-700 transition-colors"
                                    title="Bold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6z M6 12h9a4 4 0 014 4 4 4 0 01-4 4H6z" />
                                    </svg>
                                </button>
                                <button type="button" @click="insertFormat('*', '*')"
                                    class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-700 transition-colors"
                                    title="Italic">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 4h4m-2 0v16m-4 0h8" />
                                    </svg>
                                </button>
                                <button type="button" @click="insertText('\n- ')"
                                    class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-700 transition-colors"
                                    title="List">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                    </svg>
                                </button>
                                <button type="button" @click="insertText('\n## ')"
                                    class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-700 transition-colors"
                                    title="Heading">
                                    <span class="text-xs font-bold">H</span>
                                </button>
                                <button type="button" @click="insertText('\n---\n')"
                                    class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-700 transition-colors"
                                    title="Divider">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 12h14" />
                                    </svg>
                                </button>
                                <div class="h-4 w-px bg-slate-200 mx-1"></div>
                                <button type="button" @click="insertVariable('{recipient_name}')"
                                    class="px-2 py-1 rounded-lg text-[11px] font-semibold hover:bg-indigo-50 transition-colors"
                                    style="color: var(--accent);" title="{{ __('Insert recipient name') }}">
                                    +{{ __('Name') }}
                                </button>
                                <button type="button" @click="insertVariable('{sender_name}')"
                                    class="px-2 py-1 rounded-lg text-[11px] font-semibold hover:bg-indigo-50 transition-colors"
                                    style="color: var(--accent);" title="{{ __('Insert sender name') }}">
                                    +{{ __('Sender') }}
                                </button>
                                <div class="flex-1"></div>
                                <span
                                    class="text-[11px] text-slate-400">{{ __('Supports Markdown + Variables') }}</span>
                            </div>

                            <textarea x-ref="bodyEditor" x-model="body" name="body" rows="16"
                                class="w-full border-0 text-sm p-0 focus:ring-0 resize-none placeholder:text-slate-400 leading-relaxed font-mono"
                                placeholder="{{ __("Write your email content here...\n\nUse {recipient_name} to auto-insert the recipient's name.\nYou can use **bold**, *italic*, and - bullet lists") }}"
                                required>{{ old('body') }}</textarea>
                            @error('body') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Preview Tab --}}
                    <div x-show="activeTab === 'preview'" x-cloak>
                        <div class="p-5">
                            <div class="mx-auto" style="max-width: 620px;">
                                {{-- Email Preview Frame --}}
                                <div class="rounded-xl border border-slate-200 overflow-hidden"
                                    style="box-shadow: var(--shadow-md);">
                                    {{-- Fake browser bar --}}
                                    <div
                                        class="bg-slate-100 px-4 py-2.5 flex items-center gap-2 border-b border-slate-200">
                                        <div class="flex gap-1.5">
                                            <div class="w-3 h-3 rounded-full bg-rose-400"></div>
                                            <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                                            <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                                        </div>
                                        <div
                                            class="flex-1 bg-white rounded-md px-3 py-1 text-[11px] text-slate-400 text-center">
                                            {{ __('Email Preview') }}
                                        </div>
                                    </div>

                                    {{-- Email header --}}
                                    <div class="p-5" style="background: var(--gradient);">
                                        <img src="/images/logo-full.png" alt="Aperlex" class="h-8 brightness-0 invert">
                                    </div>

                                    {{-- Subject --}}
                                    <div class="px-6 pt-5">
                                        <h2 class="text-lg font-bold text-slate-900"
                                            x-html="previewSubject || subject || '{{ __('(No subject)') }}'"></h2>
                                    </div>

                                    {{-- Body --}}
                                    <div class="px-6 py-4">
                                        <div x-show="previewLoading" class="py-8 text-center">
                                            <svg class="w-6 h-6 animate-spin mx-auto text-indigo-500" fill="none"
                                                viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4" />
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                                            </svg>
                                        </div>
                                        <div x-show="!previewLoading"
                                            class="prose prose-sm max-w-none text-slate-700 leading-relaxed"
                                            style="font-size:14px;"
                                            x-html="previewHtml || '<p class=\'text-slate-400\'>{{ __('Start writing to see a preview...') }}</p>'">
                                        </div>
                                    </div>

                                    {{-- Footer --}}
                                    <div class="px-6 py-4 border-t border-slate-100 text-center">
                                        <img src="/images/logo-full.png" alt="Aperlex"
                                            class="h-5 mx-auto opacity-40 mb-2">
                                        <p class="text-[11px] text-slate-300">© {{ date('Y') }} Aperlex.
                                            {{ __('All rights reserved.') }}</p>
                                    </div>
                                </div>

                                <p class="text-center text-xs text-slate-400 mt-3">
                                    <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M13 16h-1v-4h-1m1-4h.01" />
                                        <circle cx="12" cy="12" r="10" />
                                    </svg>
                                    {{ __('Variables like {recipient_name} will be replaced with actual names when sending.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Actions --}}
                    <div
                        class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <button type="submit" :disabled="!canSend()"
                                class="btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                {{ __('Send Email') }}
                            </button>
                            <button type="button" @click="clearAll()"
                                class="btn-ghost text-sm text-slate-400 hover:text-red-500">
                                {{ __('Clear') }}
                            </button>
                        </div>
                        <div class="hidden sm:flex items-center gap-4 text-xs text-slate-400">
                            <span x-show="selectedTemplate"
                                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-indigo-50 text-indigo-600 font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4" />
                                    <circle cx="12" cy="12" r="10" />
                                </svg>
                                <span x-text="selectedTemplate"></span>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
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
                    selectedProject: '',
                    activeTab: 'write',
                    aiPrompt: '',
                    aiLoading: false,
                    aiError: null,
                    previewHtml: '',
                    previewSubject: '',
                    previewLoading: false,
                    allUsers: @json($users),

                    variables: [
                        { key: '{recipient_name}', preview: '{{ __("Recipient's name") }}' },
                        { key: '{sender_name}', preview: '{{ Auth::user()->name ?? "Admin" }}' },
                        { key: '{project_name}', preview: '{{ __("Project name") }}' },
                        { key: '{task_name}', preview: '{{ __("Task name") }}' },
                        { key: '{date}', preview: '{{ now()->format("d/m/Y") }}' },
                        { key: '{time}', preview: '{{ now()->format("H:i") }}' },
                        { key: '{app_name}', preview: 'Aperlex' },
                    ],

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
                                if (data.body) {
                                    // Auto-fill project name if selected
                                    let body = data.body;
                                    if (this.selectedProject) {
                                        body = body.replace(/\{project_name\}/g, this.selectedProject);
                                    }
                                    this.body = body;
                                }
                            });
                    },

                    onProjectChange() {
                        if (this.selectedProject) {
                            // Replace {project_name} in current body and subject
                            this.body = this.body.replace(/\{project_name\}/g, this.selectedProject);
                            this.subject = this.subject.replace(/\{project_name\}/g, this.selectedProject);
                        }
                    },

                    insertVariable(varKey) {
                        this.insertText(varKey);
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

                    clearAll() {
                        if (!confirm('{{ __("Clear all fields?") }}')) return;
                        this.recipients = [];
                        this.subject = '';
                        this.body = '';
                        this.selectedTemplate = null;
                        this.selectedProject = '';
                        this.previewHtml = '';
                        this.previewSubject = '';
                    },

                    refreshPreview() {
                        if (!this.body.trim()) {
                            this.previewHtml = '';
                            this.previewSubject = '';
                            return;
                        }
                        this.previewLoading = true;
                        fetch('{{ route('admin.email-composer.preview') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ body: this.body, subject: this.subject }),
                        })
                            .then(r => r.json())
                            .then(data => {
                                this.previewHtml = data.html || '';
                                this.previewSubject = data.subject || '';
                            })
                            .catch(() => {
                                this.previewHtml = '<p style="color:#ef4444;">{{ __("Preview failed.") }}</p>';
                            })
                            .finally(() => {
                                this.previewLoading = false;
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
                            .catch(() => {
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