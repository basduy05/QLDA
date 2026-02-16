<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm text-slate-500">{{ __('Messenger') }}</p>
                <h2 class="text-2xl font-semibold text-slate-900">{{ __('Chats & Groups') }}</h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('chat-groups.index') }}" class="btn-secondary text-xs">{{ __('Manage Groups') }}</a>
                @if ($activeType === 'direct')
                    <button type="button" id="start-call-header" class="btn-primary text-xs">{{ __('Call') }}</button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="card-strong p-0 overflow-hidden bg-gradient-to-br from-white to-slate-50/70">
        <div class="grid md:grid-cols-12 h-[calc(100vh-220px)]">
            <aside class="md:col-span-4 lg:col-span-3 border-r border-slate-100 p-3 overflow-y-auto min-h-0 bg-white/70 backdrop-blur">
                <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-500 mb-2">{{ __('Direct') }}</p>
                <div class="space-y-1 mb-4">
                    @foreach ($contacts as $contact)
                        @php($meta = $directMap->get($contact->id))
                        <a href="{{ route('messenger.direct', $contact) }}" class="block rounded-xl px-3 py-2 border transition duration-200 hover:-translate-y-0.5 {{ $activeType === 'direct' && $activeTarget?->id === $contact->id ? 'border-slate-900 bg-slate-50 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-sm' }}">
                            <div class="flex items-center justify-between gap-2">
                                <p class="font-semibold text-sm text-slate-900">{{ $contact->name }}</p>
                                <span class="inline-flex h-2.5 w-2.5 rounded-full {{ $contact->isOnline() ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                            </div>
                            <p class="text-[11px] text-slate-500">{{ \Illuminate\Support\Str::limit($meta['last_message'] ?? '', 34) }}</p>
                            <p class="text-[10px] text-slate-400">{{ $contact->activityStatusLabel() }}</p>
                        </a>
                    @endforeach
                </div>

                <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-500 mb-2">{{ __('Groups') }}</p>
                <div class="space-y-1">
                    @foreach ($groups as $group)
                        <a href="{{ route('messenger.group', $group) }}" class="block rounded-xl px-3 py-2 border transition duration-200 hover:-translate-y-0.5 {{ $activeType === 'group' && $activeTarget?->id === $group->id ? 'border-slate-900 bg-slate-50 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-sm' }}">
                            <p class="font-semibold text-sm text-slate-900">{{ $group->name }}</p>
                            <p class="text-[11px] text-slate-500">{{ $group->messages_count }} {{ __('messages') }}</p>
                        </a>
                    @endforeach
                </div>
            </aside>

            <section class="md:col-span-8 lg:col-span-9 p-3 flex flex-col min-h-0 relative bg-white/40">
                @if ($activeType)
                    <div class="relative flex items-center justify-between pb-2 border-b border-slate-100">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $activeTarget->name }}</p>
                            @if ($activeType === 'direct')
                                <p class="text-[11px] text-slate-500">{{ $activeTarget->activityStatusLabel() }}</p>
                            @elseif ($activeType === 'group')
                                <p class="text-[11px] text-slate-500">{{ $groupMembers->count() }} {{ __('members') }}</p>
                            @endif
                            <p class="text-xs text-slate-500" id="typing-indicator">
                                {{ $activeType === 'direct' && $typing ? __('Typing...') : '' }}
                            </p>
                        </div>
                        @if ($activeType === 'direct')
                            <button type="button" id="start-call-inline" class="btn-secondary text-xs">{{ __('Start Call') }}</button>
                        @elseif ($activeType === 'group')
                            <button
                                type="button"
                                id="group-settings-toggle"
                                aria-expanded="false"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 hover:border-slate-300 transition"
                                title="{{ __('Group options') }}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="4" y1="7" x2="20" y2="7"></line>
                                    <line x1="4" y1="12" x2="20" y2="12"></line>
                                    <line x1="4" y1="17" x2="20" y2="17"></line>
                                </svg>
                            </button>
                        @endif
                    </div>

                    @if ($activeType === 'group')
                        @php($groupMemberIds = $groupMembers->pluck('id')->map(fn ($id) => (int) $id)->all())
                        <div id="group-settings-backdrop" class="hidden absolute inset-0 z-10 bg-slate-900/10 rounded-2xl"></div>

                        <div id="group-settings-panel" class="absolute left-3 right-3 top-[56px] z-20 max-h-[calc(100%-76px)] overflow-y-auto rounded-2xl border border-slate-200 bg-white p-3 shadow-2xl space-y-3 origin-top-right opacity-0 scale-95 pointer-events-none transition duration-200 ease-out">
                            <form method="POST" action="{{ route('messenger.group.rename', $activeTarget) }}" class="flex items-end gap-2">
                                @csrf
                                @method('PATCH')
                                <div class="flex-1">
                                    <label for="group-name" class="text-[11px] text-slate-500">{{ __('Group name') }}</label>
                                    <input id="group-name" type="text" name="name" value="{{ old('name', $activeTarget->name) }}" class="mt-1 w-full rounded-xl border-slate-200 text-sm" maxlength="255" required>
                                </div>
                                <button type="submit" class="btn-secondary text-xs">{{ __('Rename group') }}</button>
                            </form>

                            <form method="POST" action="{{ route('messenger.group.members', $activeTarget) }}" class="space-y-2 rounded-xl border border-slate-200 p-3">
                                @csrf
                                @method('PATCH')
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Members') }}</p>
                                    <p class="text-xs text-slate-500">{{ __('Tick to add / untick to remove') }}</p>
                                </div>

                                <div class="grid sm:grid-cols-2 gap-2 max-h-52 overflow-y-auto pr-1">
                                    <label class="flex items-start gap-2 rounded-lg border border-slate-200 px-3 py-2 bg-slate-50">
                                        <input type="checkbox" checked disabled class="mt-1 rounded border-slate-300 text-slate-900">
                                        <input type="hidden" name="member_ids[]" value="{{ auth()->id() }}">
                                        <span>
                                            <span class="block text-sm font-medium text-slate-800">{{ auth()->user()->name }} {{ __('(You)') }}</span>
                                            <span class="block text-[11px] text-slate-500">{{ auth()->user()->email }}</span>
                                        </span>
                                    </label>

                                    @foreach ($contacts as $contact)
                                        <label class="flex items-start gap-2 rounded-lg border border-slate-200 px-3 py-2 cursor-pointer hover:border-slate-300">
                                            <input type="checkbox" name="member_ids[]" value="{{ $contact->id }}" class="mt-1 rounded border-slate-300 text-slate-900" @checked(in_array((int) $contact->id, $groupMemberIds, true))>
                                            <span>
                                                <span class="block text-sm font-medium text-slate-800">{{ $contact->name }}</span>
                                                <span class="block text-[11px] text-slate-500">{{ $contact->email }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>

                                <button type="submit" class="btn-secondary text-xs">{{ __('Save members') }}</button>
                            </form>

                            <div class="flex flex-wrap gap-2">
                                @foreach ($groupMembers as $member)
                                    @php($memberName = $groupNicknames[$member->id] ?? $member->name)
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] text-slate-700">
                                        <span class="inline-flex h-2 w-2 rounded-full {{ $member->isOnline() ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                                        <span>{{ $memberName }}</span>
                                    </span>
                                @endforeach
                            </div>

                            <div class="grid md:grid-cols-2 gap-2">
                                @foreach ($groupMembers as $member)
                                    @php($memberName = $groupNicknames[$member->id] ?? $member->name)
                                    <form method="POST" action="{{ route('messenger.group.member-nickname', [$activeTarget, $member]) }}" class="flex items-end gap-2 rounded-xl border border-slate-200 p-2">
                                        @csrf
                                        @method('PATCH')
                                        <div class="flex-1">
                                            <p class="text-[11px] text-slate-500">{{ __('Nickname for :name', ['name' => $member->name]) }}</p>
                                            <input type="text" name="nickname" value="{{ old('nickname_'.$member->id, $member->pivot->nickname ?? '') }}" maxlength="40" class="mt-1 w-full rounded-xl border-slate-200 text-sm" placeholder="{{ __('Current: :nick', ['nick' => $memberName]) }}">
                                        </div>
                                        <button type="submit" class="btn-secondary text-xs">{{ __('Save') }}</button>
                                    </form>
                                @endforeach
                            </div>

                            @if ($canDeleteGroup)
                                <form method="POST" action="{{ route('chat-groups.destroy', $activeTarget) }}" class="pt-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-secondary text-xs text-red-600 border-red-200 hover:border-red-300">{{ __('Delete group') }}</button>
                                </form>
                            @endif
                        </div>
                    @endif

                    <div id="messenger-feed" class="flex-1 overflow-y-auto py-3 pr-1 space-y-2">
                        @if ($activeType === 'direct')
                            @include('messenger.partials.direct-feed', ['messages' => $messages, 'authUserId' => auth()->id()])
                        @else
                            @include('messenger.partials.group-feed', ['messages' => $messages, 'authUserId' => auth()->id(), 'nicknames' => $groupNicknames])
                        @endif
                    </div>

                    <form id="composer-form" method="POST" action="{{ $activeType === 'direct' ? route('messenger.send-direct', $activeTarget) : route('messenger.send-group', $activeTarget) }}" class="pt-2 border-t border-slate-100 bg-white/70 backdrop-blur rounded-2xl px-2 pb-1">
                        @csrf
                        <div class="flex items-end gap-2">
                            <textarea id="composer" name="body" rows="2" class="w-full rounded-xl border-slate-200" placeholder="{{ __('Type a message...') }}" required>{{ old('body') }}</textarea>
                            <button id="composer-submit" type="submit" class="btn-primary">{{ __('Send') }}</button>
                        </div>
                        @error('body')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                        <p id="composer-error" class="text-sm text-red-600 mt-2 hidden"></p>
                        <p class="text-[11px] text-slate-400 mt-1">{{ __('Enter to send · Ctrl+Enter for new line') }}</p>
                    </form>
                @else
                    <div class="h-full flex items-center justify-center text-sm text-slate-500">
                        {{ __('Choose a chat or group to start messaging.') }}
                    </div>
                @endif
            </section>
        </div>
    </div>

    @if ($activeType === 'direct')
           <div id="call-data"
               class="hidden"
               data-contact-id="{{ $activeTarget->id }}"
               data-auth-id="{{ auth()->id() }}"
               data-ice='@json(config("webrtc.ice_servers"))'></div>

        <div id="call-modal" class="fixed inset-0 z-50 hidden bg-slate-900/75 p-4 backdrop-blur-sm">
            <div class="mx-auto h-full max-w-6xl rounded-2xl bg-white shadow-xl flex flex-col overflow-hidden border border-slate-200">
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                    <div>
                        <h3 id="call-title" class="font-semibold text-slate-900">{{ __('Call') }}</h3>
                        <p id="call-status-text" class="text-xs text-slate-500 mt-1"></p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" id="accept-call" class="btn-primary text-xs hidden">{{ __('Accept') }}</button>
                        <button type="button" id="reject-call" class="btn-secondary text-xs hidden">{{ __('Reject') }}</button>
                        <button type="button" id="end-call" class="btn-secondary text-xs hidden">{{ __('End') }}</button>
                        <button type="button" id="close-call" class="btn-secondary text-xs">{{ __('Close') }}</button>
                    </div>
                </div>
                <div class="grid md:grid-cols-2 gap-3 p-4 bg-slate-50">
                    <div class="card p-3">
                        <p class="text-xs text-slate-500 mb-2">{{ __('Remote') }}</p>
                        <video id="remote-video" class="w-full rounded-xl bg-black" autoplay playsinline></video>
                    </div>
                    <div class="card p-3">
                        <p class="text-xs text-slate-500 mb-2">{{ __('You') }}</p>
                        <video id="local-video" class="w-full rounded-xl bg-black" autoplay playsinline muted></video>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($activeType)
        <div
            id="messenger-data"
            class="hidden"
            data-ws-url="{{ $wsUrl }}"
            data-ws-channels='@json(array_values(array_filter(array_merge([$wsUserChannel], $wsGroupChannels->all()))))'
            data-has-typing="{{ $typing ? 1 : 0 }}"
            data-active-target-id="{{ $activeType === 'direct' ? $activeTarget->id : 0 }}"
        ></div>

        <script>
            (function () {
                const feed = document.getElementById('messenger-feed');
                const composer = document.getElementById('composer');
                const form = document.getElementById('composer-form');
                const submitBtn = document.getElementById('composer-submit');
                const composerError = document.getElementById('composer-error');
                const typingEl = document.getElementById('typing-indicator');
                const groupSettingsToggle = document.getElementById('group-settings-toggle');
                const groupSettingsPanel = document.getElementById('group-settings-panel');
                const groupSettingsBackdrop = document.getElementById('group-settings-backdrop');
                const runtime = document.getElementById('messenger-data');
                const type = "{{ $activeType }}";
                const feedEndpoint = type === 'direct'
                    ? "{{ route('messenger.direct-feed', $activeTarget) }}"
                    : "{{ route('messenger.group-feed', $activeTarget) }}";
                const typingEndpoint = type === 'direct'
                    ? "{{ route('messenger.typing', $activeTarget) }}"
                    : '';
                const wsUrl = runtime?.dataset.wsUrl || '';
                const wsChannels = JSON.parse(runtime?.dataset.wsChannels || '[]');
                const hasInitialTyping = runtime?.dataset.hasTyping === '1';
                const activeTargetId = Number(runtime?.dataset.activeTargetId || 0);
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                let loading = false;
                let queuedRefresh = false;
                let typingTick = 0;
                let typingTimeout = null;
                let submitLocked = false;
                let lastSubmitAt = 0;
                let unlockGuard = null;
                let lastFeedHtml = feed ? feed.innerHTML : '';

                if (groupSettingsToggle && groupSettingsPanel) {
                    const openGroupSettings = () => {
                        groupSettingsPanel.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
                        groupSettingsPanel.classList.add('opacity-100', 'scale-100', 'pointer-events-auto');
                        groupSettingsBackdrop?.classList.remove('hidden');
                        groupSettingsToggle.setAttribute('aria-expanded', 'true');
                    };

                    const closeGroupSettings = () => {
                        groupSettingsPanel.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                        groupSettingsPanel.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
                        groupSettingsBackdrop?.classList.add('hidden');
                        groupSettingsToggle.setAttribute('aria-expanded', 'false');
                    };

                    groupSettingsToggle.addEventListener('click', function () {
                        const expanded = groupSettingsToggle.getAttribute('aria-expanded') === 'true';
                        if (expanded) {
                            closeGroupSettings();
                            return;
                        }

                        openGroupSettings();
                    });

                    groupSettingsBackdrop?.addEventListener('click', closeGroupSettings);

                    document.addEventListener('keydown', function (event) {
                        if (event.key === 'Escape') {
                            closeGroupSettings();
                        }
                    });
                }

                const setSubmitState = (busy) => {
                    submitLocked = busy;

                    if (submitBtn) {
                        submitBtn.disabled = busy;
                        submitBtn.classList.toggle('opacity-70', busy);
                        submitBtn.textContent = busy ? "{{ __('Sending...') }}" : "{{ __('Send') }}";
                    }

                    if (composer) {
                        composer.readOnly = busy;
                    }
                };

                const showComposerError = (message) => {
                    if (!composerError) {
                        return;
                    }

                    if (!message) {
                        composerError.classList.add('hidden');
                        composerError.textContent = '';
                        return;
                    }

                    composerError.classList.remove('hidden');
                    composerError.textContent = message;
                };

                const scheduleRefresh = () => {
                    if (queuedRefresh) {
                        return;
                    }

                    queuedRefresh = true;
                    setTimeout(() => {
                        queuedRefresh = false;
                        refresh();
                    }, 120);
                };

                const fetchWithTimeout = async (url, options = {}, timeoutMs = 9000) => {
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), timeoutMs);

                    try {
                        return await fetch(url, {
                            ...options,
                            signal: controller.signal,
                        });
                    } finally {
                        clearTimeout(timeoutId);
                    }
                };

                const wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

                const refresh = async () => {
                    if (loading || !feed) {
                        return;
                    }

                    loading = true;
                    const nearBottom = feed.scrollHeight - feed.scrollTop - feed.clientHeight < 120;

                    try {
                        const response = await fetch(feedEndpoint, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin',
                        });

                        if (!response.ok) {
                            return;
                        }

                        const payload = await response.json();
                        if (payload.html !== lastFeedHtml) {
                            feed.innerHTML = payload.html;
                            lastFeedHtml = payload.html;
                        }

                        if (type === 'direct' && typingEl) {
                            typingEl.textContent = payload.typing ? "{{ __('Typing...') }}" : '';
                        }

                        if (nearBottom) {
                            feed.scrollTop = feed.scrollHeight;
                        }
                    } finally {
                        loading = false;
                    }
                };

                if (composer) {
                    composer.addEventListener('keydown', function (event) {
                        if (event.key === 'Enter' && !event.ctrlKey) {
                            event.preventDefault();

                            if (submitLocked) {
                                return;
                            }

                            form?.requestSubmit();
                        }
                    });

                    composer.addEventListener('input', function () {
                        if (type !== 'direct') {
                            return;
                        }

                        const now = Date.now();
                        if (now - typingTick < 2000) {
                            return;
                        }

                        typingTick = now;

                        fetch(typingEndpoint, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrf,
                            },
                            credentials: 'same-origin',
                        });
                    });
                }

                if (form && composer) {
                    form.addEventListener('submit', async function (event) {
                        event.preventDefault();

                        if (submitLocked) {
                            return;
                        }

                        const now = Date.now();
                        if (now - lastSubmitAt < 700) {
                            return;
                        }
                        lastSubmitAt = now;

                        const body = (composer.value || '').trim();
                        if (!body) {
                            showComposerError("{{ __('Message cannot be empty.') }}");
                            return;
                        }

                        showComposerError('');
                        setSubmitState(true);

                        clearTimeout(unlockGuard);
                        unlockGuard = setTimeout(() => {
                            setSubmitState(false);
                            showComposerError("{{ __('Send request timed out. Please try again.') }}");
                        }, 15000);

                        try {
                            let response = null;

                            for (let attempt = 0; attempt < 2; attempt += 1) {
                                try {
                                    const formData = new FormData(form);
                                    formData.set('body', body);

                                    response = await fetchWithTimeout(form.action, {
                                        method: 'POST',
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': csrf,
                                        },
                                        credentials: 'same-origin',
                                        body: formData,
                                    }, 9000);
                                    break;
                                } catch (error) {
                                    if (attempt === 0) {
                                        await wait(300);
                                        continue;
                                    }

                                    throw error;
                                }
                            }

                            if (!response) {
                                throw new Error('send-no-response');
                            }

                            if (response.status === 422) {
                                const payload = await response.json();
                                const message = payload?.errors?.body?.[0] || "{{ __('Unable to send message.') }}";
                                showComposerError(message);
                                return;
                            }

                            if (!response.ok) {
                                throw new Error('send-failed');
                            }

                            composer.value = '';
                            composer.focus();
                            scheduleRefresh();
                        } catch (_) {
                            showComposerError("{{ __('Network is unstable. Please try sending again.') }}");
                        } finally {
                            clearTimeout(unlockGuard);
                            setSubmitState(false);
                        }
                    });
                }

                if (feed) {
                    feed.scrollTop = feed.scrollHeight;
                    setInterval(scheduleRefresh, 12000);
                }

                const triggerTyping = () => {
                    if (!typingEl || type !== 'direct') {
                        return;
                    }

                    typingEl.textContent = "{{ __('Typing...') }}";
                    if (typingTimeout) {
                        clearTimeout(typingTimeout);
                    }
                    typingTimeout = setTimeout(() => {
                        typingEl.textContent = '';
                    }, 3000);
                };

                const bindSocket = () => {
                    if (!wsUrl || !Array.isArray(wsChannels) || wsChannels.length === 0) {
                        return;
                    }

                    let socket;

                    const connect = () => {
                        try {
                            const query = wsChannels
                                .map((channel) => 'channel=' + encodeURIComponent(channel))
                                .join('&');
                            const separator = wsUrl.includes('?') ? '&' : '?';
                            socket = new WebSocket(wsUrl + separator + query);
                        } catch (_) {
                            return;
                        }

                        socket.addEventListener('open', () => {
                            try {
                                socket.send(JSON.stringify({ action: 'subscribe', channels: wsChannels }));
                            } catch (_) {
                            }
                        });

                        socket.addEventListener('message', (event) => {
                            try {
                                const message = JSON.parse(event.data || '{}');
                                const eventName = message?.event || '';

                                if (eventName === 'typing.direct' && type === 'direct') {
                                    const fromId = Number(message?.payload?.from_id || 0);
                                    if (fromId === activeTargetId) {
                                        triggerTyping();
                                    }
                                    return;
                                }

                                if (eventName === 'message.direct' || eventName === 'message.group') {
                                    scheduleRefresh();
                                }
                            } catch (_) {
                            }
                        });

                        socket.addEventListener('close', () => {
                            setTimeout(connect, 1800);
                        });

                        socket.addEventListener('error', () => {
                            try {
                                socket.close();
                            } catch (_) {
                            }
                        });
                    };

                    connect();
                };

                bindSocket();
                if (type === 'direct' && hasInitialTyping) {
                    triggerTyping();
                }
            })();
        </script>
    @endif

    @if ($activeType === 'direct')
        <script>
            (function () {
                const modal = document.getElementById('call-modal');
                const startHeader = document.getElementById('start-call-header');
                const startInline = document.getElementById('start-call-inline');
                const acceptBtn = document.getElementById('accept-call');
                const rejectBtn = document.getElementById('reject-call');
                const endBtn = document.getElementById('end-call');
                const closeBtn = document.getElementById('close-call');
                const titleNode = document.getElementById('call-title');
                const statusNode = document.getElementById('call-status-text');
                const remoteVideo = document.getElementById('remote-video');
                const localVideo = document.getElementById('local-video');
                const callData = document.getElementById('call-data');

                const contactId = Number(callData?.dataset.contactId || 0);
                const authId = Number(callData?.dataset.authId || 0);
                const iceServers = JSON.parse(callData?.dataset.ice || '[]');
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                let currentCall = null;
                let pc = null;
                let localStream = null;
                let ringingCtx = null;
                let ringingOsc = null;
                let locked = false;
                let callActionLocked = false;

                const setCallStatus = (message = '', tone = 'normal') => {
                    if (!statusNode) {
                        return;
                    }

                    statusNode.textContent = message;
                    statusNode.classList.remove('text-slate-500', 'text-red-600', 'text-emerald-600');
                    statusNode.classList.add(tone === 'error' ? 'text-red-600' : tone === 'success' ? 'text-emerald-600' : 'text-slate-500');
                };

                const setCallActionBusy = (busy) => {
                    callActionLocked = busy;
                    [startHeader, startInline, acceptBtn, rejectBtn, endBtn].forEach((button) => {
                        if (!button) {
                            return;
                        }

                        button.disabled = busy;
                        button.classList.toggle('opacity-70', busy);
                    });
                };

                const api = async (url, options = {}) => {
                    const headers = Object.assign({
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf,
                    }, options.headers || {});

                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 10000);

                    let response;
                    try {
                        response = await fetch(url, Object.assign({
                            credentials: 'same-origin',
                            headers,
                            signal: controller.signal,
                        }, options));
                    } finally {
                        clearTimeout(timeoutId);
                    }

                    if (!response.ok) {
                        let message = 'Request failed';
                        try {
                            const payload = await response.json();
                            message = payload?.message || message;
                        } catch (_) {
                        }

                        throw new Error(message);
                    }

                    try {
                        return await response.json();
                    } catch (_) {
                        throw new Error('Request failed');
                    }
                };

                const closeModal = () => {
                    modal?.classList.add('hidden');
                    setCallStatus('');
                };

                const stopRingtone = () => {
                    try { ringingOsc?.stop(); } catch (_) {}
                    ringingOsc = null;
                    if (ringingCtx) {
                        ringingCtx.close();
                        ringingCtx = null;
                    }
                };

                const startRingtone = () => {
                    if (ringingCtx) return;
                    try {
                        ringingCtx = new (window.AudioContext || window.webkitAudioContext)();
                        ringingOsc = ringingCtx.createOscillator();
                        const gain = ringingCtx.createGain();
                        ringingOsc.frequency.value = 660;
                        gain.gain.value = 0.05;
                        ringingOsc.connect(gain);
                        gain.connect(ringingCtx.destination);
                        ringingOsc.start();
                    } catch (_) {}
                };

                const resetPeer = () => {
                    if (pc) {
                        try { pc.close(); } catch (_) {}
                    }
                    pc = null;

                    if (localStream) {
                        localStream.getTracks().forEach((track) => track.stop());
                    }
                    localStream = null;

                    if (localVideo) localVideo.srcObject = null;
                    if (remoteVideo) remoteVideo.srcObject = null;
                };

                const ensurePeer = async () => {
                    if (pc) return pc;

                    if (!window.isSecureContext) {
                        throw new Error("{{ __('Calling requires HTTPS (secure context).') }}");
                    }

                    if (!navigator.mediaDevices?.getUserMedia) {
                        throw new Error("{{ __('Camera/Microphone is not supported on this browser.') }}");
                    }

                    localStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: true });
                    if (localVideo) localVideo.srcObject = localStream;

                    pc = new RTCPeerConnection({ iceServers });
                    localStream.getTracks().forEach((track) => pc.addTrack(track, localStream));

                    pc.ontrack = (event) => {
                        if (remoteVideo) remoteVideo.srcObject = event.streams[0];
                    };

                    pc.onconnectionstatechange = () => {
                        if (!pc) {
                            return;
                        }

                        if (['failed', 'disconnected'].includes(pc.connectionState) && currentCall?.id) {
                            end();
                        }
                    };

                    return pc;
                };

                const waitIceComplete = (peer) => new Promise((resolve) => {
                    if (peer.iceGatheringState === 'complete') {
                        resolve();
                        return;
                    }

                    const handler = () => {
                        if (peer.iceGatheringState === 'complete') {
                            peer.removeEventListener('icegatheringstatechange', handler);
                            resolve();
                        }
                    };

                    peer.addEventListener('icegatheringstatechange', handler);
                });

                const showIncoming = () => {
                    modal?.classList.remove('hidden');
                    if (titleNode) titleNode.textContent = "{{ __('Incoming call') }}";
                    setCallStatus("{{ __('Someone is calling you.') }}");
                    acceptBtn?.classList.remove('hidden');
                    rejectBtn?.classList.remove('hidden');
                    endBtn?.classList.add('hidden');
                };

                const showActive = (titleText) => {
                    modal?.classList.remove('hidden');
                    if (titleNode) titleNode.textContent = titleText;
                    acceptBtn?.classList.add('hidden');
                    rejectBtn?.classList.add('hidden');
                    endBtn?.classList.remove('hidden');
                };

                const startCall = async () => {
                    if (callActionLocked) {
                        return;
                    }

                    setCallActionBusy(true);
                    setCallStatus("{{ __('Starting call...') }}");

                    try {
                        const payload = await api("{{ route('calls.start', $activeTarget) }}", { method: 'POST' });
                        currentCall = payload.call;
                        showActive("{{ __('Calling...') }}");
                        setCallStatus("{{ __('Waiting for answer...') }}");
                    } catch (error) {
                        setCallStatus(error.message || "{{ __('Unable to start call.') }}", 'error');
                    } finally {
                        setCallActionBusy(false);
                    }
                };

                const sync = async () => {
                    if (locked) return;
                    locked = true;

                    try {
                        const payload = await api("{{ route('calls.poll') }}");
                        const call = payload.call;

                        if (!call) {
                            if (currentCall) {
                                stopRingtone();
                                resetPeer();
                                closeModal();
                            }
                            stopRingtone();
                            currentCall = null;
                            return;
                        }

                        if (![call.caller_id, call.callee_id].includes(contactId)) {
                            return;
                        }

                        currentCall = call;

                        if (call.status === 'ringing' && call.callee_id === authId) {
                            startRingtone();
                            showIncoming();
                            return;
                        }

                        if (call.status === 'ringing' && call.caller_id === authId) {
                            modal?.classList.remove('hidden');
                            if (titleNode) titleNode.textContent = "{{ __('Calling...') }}";
                            setCallStatus("{{ __('Waiting for answer...') }}");
                            return;
                        }

                        if (call.status === 'active') {
                            stopRingtone();
                            showActive("{{ __('In call') }}");
                            setCallStatus("{{ __('Connected') }}", 'success');

                            if (call.caller_id === authId && !call.offer_sdp) {
                                const peer = await ensurePeer();
                                const offer = await peer.createOffer();
                                await peer.setLocalDescription(offer);
                                await waitIceComplete(peer);

                                await api("{{ url('/calls') }}/" + call.id + "/signal", {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({ type: 'offer', sdp: peer.localDescription.sdp }),
                                });
                            }

                            if (call.offer_sdp && call.callee_id === authId) {
                                const peer = await ensurePeer();
                                if (!peer.currentRemoteDescription) {
                                    await peer.setRemoteDescription({ type: 'offer', sdp: call.offer_sdp });
                                    const answer = await peer.createAnswer();
                                    await peer.setLocalDescription(answer);
                                    await waitIceComplete(peer);

                                    await api("{{ url('/calls') }}/" + call.id + "/signal", {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json' },
                                        body: JSON.stringify({ type: 'answer', sdp: peer.localDescription.sdp }),
                                    });
                                }
                            }

                            if (call.answer_sdp && call.caller_id === authId) {
                                const peer = await ensurePeer();
                                if (!peer.currentRemoteDescription) {
                                    await peer.setRemoteDescription({ type: 'answer', sdp: call.answer_sdp });
                                }
                            }
                        }

                        if (['ended', 'rejected', 'missed'].includes(call.status)) {
                            stopRingtone();
                            resetPeer();
                            if (call.status === 'rejected') {
                                setCallStatus("{{ __('Call was rejected.') }}", 'error');
                            } else if (call.status === 'missed') {
                                setCallStatus("{{ __('Call was missed.') }}", 'error');
                            } else {
                                setCallStatus("{{ __('Call ended.') }}");
                            }
                            closeModal();
                            currentCall = null;
                        }
                    } catch (error) {
                        setCallStatus(error.message || "{{ __('Call sync failed.') }}", 'error');
                    } finally {
                        locked = false;
                    }
                };

                const accept = async () => {
                    if (!currentCall || callActionLocked) return;
                    setCallActionBusy(true);
                    try {
                        await api("{{ url('/calls') }}/" + currentCall.id + "/accept", { method: 'POST' });
                        stopRingtone();
                        showActive("{{ __('Connecting...') }}");
                        setCallStatus("{{ __('Connecting media...') }}");
                    } catch (error) {
                        setCallStatus(error.message || "{{ __('Unable to accept call.') }}", 'error');
                    } finally {
                        setCallActionBusy(false);
                    }
                };

                const reject = async () => {
                    if (!currentCall || callActionLocked) return;
                    setCallActionBusy(true);
                    try {
                        await api("{{ url('/calls') }}/" + currentCall.id + "/reject", { method: 'POST' });
                    } catch (error) {
                        setCallStatus(error.message || "{{ __('Unable to reject call.') }}", 'error');
                    }
                    stopRingtone();
                    resetPeer();
                    closeModal();
                    setCallActionBusy(false);
                };

                const end = async () => {
                    if (!currentCall || callActionLocked) return;
                    setCallActionBusy(true);
                    try {
                        await api("{{ url('/calls') }}/" + currentCall.id + "/end", { method: 'POST' });
                    } catch (error) {
                        setCallStatus(error.message || "{{ __('Unable to end call.') }}", 'error');
                    }
                    stopRingtone();
                    resetPeer();
                    closeModal();
                    setCallActionBusy(false);
                };

                startHeader?.addEventListener('click', startCall);
                startInline?.addEventListener('click', startCall);
                acceptBtn?.addEventListener('click', accept);
                rejectBtn?.addEventListener('click', reject);
                endBtn?.addEventListener('click', end);
                closeBtn?.addEventListener('click', () => {
                    if (currentCall && ['ringing', 'active'].includes(currentCall.status)) {
                        end();
                        return;
                    }

                    closeModal();
                });

                modal?.addEventListener('click', (event) => {
                    if (event.target === modal) closeModal();
                });

                setInterval(sync, 2000);
            })();
        </script>
    @endif
</x-app-layout>
