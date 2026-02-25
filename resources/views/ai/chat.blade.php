<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm text-slate-500">{{ __('AI Assistant') }}</p>
                <h2 class="text-2xl font-semibold text-slate-900">{{ __('Project AI Chat') }}</h2>
            </div>
            <a href="{{ route('messenger.index', ['popup' => request()->query('popup')]) }}" class="btn-secondary text-xs">{{ __('Back to Messenger') }}</a>
        </div>
    </x-slot>

    <div class="card-strong p-0 overflow-hidden {{ request()->query('popup') ? 'h-screen rounded-none border-0 flex flex-col' : '' }}">
        <div class="grid lg:grid-cols-12 {{ request()->query('popup') ? 'flex-1 overflow-hidden' : 'min-h-[70vh]' }}">
            <aside class="lg:col-span-4 border-b lg:border-b-0 lg:border-r border-slate-100 p-4 space-y-3 bg-white/80 {{ request()->query('popup') ? 'overflow-y-auto max-h-[30vh] lg:max-h-none' : '' }}">
                <div>
                    <label for="ai-project" class="text-sm font-medium text-slate-700">{{ __('Project context') }}</label>
                    <select id="ai-project" class="mt-2 w-full rounded-xl border-slate-200 text-sm">
                        <option value="">{{ __('No project selected') }}</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }} ({{ __($project->status) }})</option>
                        @endforeach
                    </select>
                </div>

                @if ($isAdmin)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs text-slate-600 space-y-1">
                        <p><span class="font-semibold">{{ __('Model') }}:</span> {{ $defaultModel }}</p>
                        <p><span class="font-semibold">{{ __('API status') }}:</span> {{ $hasApiKey ? __('Configured') : __('Not configured') }}</p>
                        @unless($hasApiKey)
                            <p class="text-rose-600">{{ __('Please ask admin to add Gemini API key in AI Settings.') }}</p>
                        @endunless
                    </div>
                @endif

                <div class="rounded-xl border border-slate-200 bg-white p-3">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">{{ __('Quick prompts') }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($quickPrompts as $prompt)
                            <button type="button" class="ai-prompt-btn rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs text-slate-700 hover:border-slate-300" data-prompt="{{ $prompt }}">{{ $prompt }}</button>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-3">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">{{ __('Task suggestions') }}</p>
                    <ul id="ai-suggestion-list" class="space-y-2 text-sm text-slate-700">
                        @foreach ($taskSuggestions as $suggestion)
                            <li>
                                <button type="button" class="ai-suggestion-btn w-full text-left rounded-lg border border-slate-200 px-3 py-2 hover:border-slate-300" data-prompt="{{ $suggestion }}">{{ $suggestion }}</button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>

            <section class="lg:col-span-8 p-4 flex flex-col {{ request()->query('popup') ? 'flex-1 min-h-0' : 'min-h-[55vh]' }}">
                <div id="ai-feed" class="flex-1 overflow-y-auto space-y-3 pr-1">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                        {{ __('Xin chào! Bạn có thể hỏi về kế hoạch công việc, phân chia task, rủi ro deadline, hoặc gợi ý nội dung trao đổi trong nhóm.') }}
                    </div>
                </div>

                <form id="ai-form" class="mt-3 border-t border-slate-100 pt-3">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-end gap-2">
                        <textarea id="ai-input" rows="3" class="w-full rounded-xl border-slate-200 text-sm" placeholder="{{ __('Ask AI assistant...') }}"></textarea>
                        <button id="ai-submit" type="submit" class="btn-primary sm:shrink-0">{{ __('Send') }}</button>
                    </div>
                    <p id="ai-error" class="mt-2 text-sm text-red-600 hidden"></p>
                </form>
            </section>
        </div>
    </div>

    <script>
        (function () {
            const form = document.getElementById('ai-form');
            const input = document.getElementById('ai-input');
            const submit = document.getElementById('ai-submit');
            const feed = document.getElementById('ai-feed');
            const errorNode = document.getElementById('ai-error');
            const projectNode = document.getElementById('ai-project');
            const quickPromptButtons = Array.from(document.querySelectorAll('.ai-prompt-btn, .ai-suggestion-btn'));
            const suggestionList = document.getElementById('ai-suggestion-list');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            let locked = false;

            const addBubble = (text, mine = false, typewriter = false) => {
                const row = document.createElement('div');
                row.className = `flex ${mine ? 'justify-end' : 'justify-start'}`;

                const bubble = document.createElement('div');
                bubble.className = `max-w-[92%] md:max-w-[78%] rounded-2xl px-4 py-2 text-sm whitespace-pre-wrap ${mine ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-800'}`;
                
                if (typewriter && !mine) {
                    bubble.textContent = '';
                    row.appendChild(bubble);
                    feed?.appendChild(row);
                    
                    let i = 0;
                    const speed = 15; // ms per character
                    
                    const typeWriterEffect = () => {
                        if (i < text.length) {
                            bubble.textContent += text.charAt(i);
                            i++;
                            if (feed) feed.scrollTop = feed.scrollHeight;
                            setTimeout(typeWriterEffect, speed);
                        }
                    };
                    typeWriterEffect();
                } else {
                    bubble.textContent = text;
                    row.appendChild(bubble);
                    feed?.appendChild(row);
                    if (feed) {
                        feed.scrollTop = feed.scrollHeight;
                    }
                }
            };

            const setError = (message) => {
                if (!errorNode) return;
                if (!message) {
                    errorNode.textContent = '';
                    errorNode.classList.add('hidden');
                    return;
                }
                errorNode.textContent = message;
                errorNode.classList.remove('hidden');
            };

            const fetchSuggestions = async () => {
                try {
                    const response = await fetch("{{ route('ai.suggestions') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            project_id: projectNode?.value || null,
                        }),
                    });

                    const payload = await response.json().catch(() => ({}));
                    if (!response.ok || !payload?.ok || !Array.isArray(payload?.suggestions) || !suggestionList) {
                        return;
                    }

                    suggestionList.innerHTML = '';
                    payload.suggestions.forEach((suggestion) => {
                        const li = document.createElement('li');
                        li.innerHTML = `<button type="button" class="ai-suggestion-btn w-full text-left rounded-lg border border-slate-200 px-3 py-2 hover:border-slate-300" data-prompt="${String(suggestion).replace(/"/g, '&quot;')}">${suggestion}</button>`;
                        suggestionList.appendChild(li);
                    });

                    suggestionList.querySelectorAll('.ai-suggestion-btn').forEach((button) => {
                        button.addEventListener('click', () => {
                            if (!input) return;
                            input.value = button.dataset.prompt || '';
                            input.focus();
                        });
                    });
                } catch (_) {
                }
            };

            projectNode?.addEventListener('change', fetchSuggestions);

            quickPromptButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    if (!input) return;
                    input.value = button.dataset.prompt || '';
                    input.focus();
                });
            });

            form?.addEventListener('submit', async function (event) {
                event.preventDefault();
                if (locked) return;

                const message = (input?.value || '').trim();
                if (!message) {
                    setError("{{ __('Message cannot be empty.') }}");
                    return;
                }

                setError('');
                addBubble(message, true);
                if (input) input.value = '';

                locked = true;
                if (submit) {
                    submit.disabled = true;
                    submit.textContent = "{{ __('Sending...') }}";
                }

                try {
                    const response = await fetch("{{ route('ai.chat') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            message,
                            project_id: projectNode?.value || null,
                        }),
                    });

                    const payload = await response.json().catch(() => ({}));

                    if (!response.ok || !payload?.ok) {
                        setError(payload?.message || "{{ __('AI request failed. Please try again later.') }}");
                        return;
                    }

                    addBubble(payload.reply || "{{ __('AI returned an empty response.') }}", false, true);
                } catch (_) {
                    setError("{{ __('AI service is temporarily unavailable.') }}");
                } finally {
                    locked = false;
                    if (submit) {
                        submit.disabled = false;
                        submit.textContent = "{{ __('Send') }}";
                    }
                }
            });
        })();
    </script>
</x-app-layout>
