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

    <div class="card-strong p-0 overflow-hidden">
        <div class="grid md:grid-cols-12 h-[calc(100vh-220px)]">
            <aside class="md:col-span-4 lg:col-span-3 border-r border-slate-100 p-3 overflow-y-auto min-h-0">
                <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-500 mb-2">{{ __('Direct') }}</p>
                <div class="space-y-1 mb-4">
                    @foreach ($contacts as $contact)
                        @php($meta = $directMap->get($contact->id))
                        <a href="{{ route('messenger.direct', $contact) }}" class="block rounded-xl px-3 py-2 border {{ $activeType === 'direct' && $activeTarget?->id === $contact->id ? 'border-slate-900 bg-slate-50' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                            <p class="font-semibold text-sm text-slate-900">{{ $contact->name }}</p>
                            <p class="text-[11px] text-slate-500">{{ \Illuminate\Support\Str::limit($meta['last_message'] ?? '', 34) }}</p>
                        </a>
                    @endforeach
                </div>

                <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-500 mb-2">{{ __('Groups') }}</p>
                <div class="space-y-1">
                    @foreach ($groups as $group)
                        <a href="{{ route('messenger.group', $group) }}" class="block rounded-xl px-3 py-2 border {{ $activeType === 'group' && $activeTarget?->id === $group->id ? 'border-slate-900 bg-slate-50' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                            <p class="font-semibold text-sm text-slate-900">{{ $group->name }}</p>
                            <p class="text-[11px] text-slate-500">{{ $group->messages_count }} {{ __('messages') }}</p>
                        </a>
                    @endforeach
                </div>
            </aside>

            <section class="md:col-span-8 lg:col-span-9 p-3 flex flex-col min-h-0">
                @if ($activeType)
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $activeTarget->name }}</p>
                            <p class="text-xs text-slate-500" id="typing-indicator">
                                {{ $activeType === 'direct' && $typing ? __('Typing...') : '' }}
                            </p>
                        </div>
                        @if ($activeType === 'direct')
                            <button type="button" id="start-call-inline" class="btn-secondary text-xs">{{ __('Start Call') }}</button>
                        @endif
                    </div>

                    <div id="messenger-feed" class="flex-1 overflow-y-auto py-3 space-y-2">
                        @if ($activeType === 'direct')
                            @include('messenger.partials.direct-feed', ['messages' => $messages, 'authUserId' => auth()->id()])
                        @else
                            @include('messenger.partials.group-feed', ['messages' => $messages, 'authUserId' => auth()->id()])
                        @endif
                    </div>

                    <form id="composer-form" method="POST" action="{{ $activeType === 'direct' ? route('messenger.send-direct', $activeTarget) : route('messenger.send-group', $activeTarget) }}" class="pt-2 border-t border-slate-100">
                        @csrf
                        <div class="flex items-end gap-2">
                            <textarea id="composer" name="body" rows="2" class="w-full rounded-xl border-slate-200" placeholder="{{ __('Type a message...') }}" required>{{ old('body') }}</textarea>
                            <button type="submit" class="btn-primary">{{ __('Send') }}</button>
                        </div>
                        @error('body')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
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

        <div id="call-modal" class="fixed inset-0 z-50 hidden bg-slate-900/70 p-4">
            <div class="mx-auto h-full max-w-6xl rounded-2xl bg-white shadow-xl flex flex-col overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                    <h3 id="call-title" class="font-semibold text-slate-900">{{ __('Call') }}</h3>
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
                const typingEl = document.getElementById('typing-indicator');
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
                let typingTick = 0;
                let typingTimeout = null;

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
                        feed.innerHTML = payload.html;

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
                            form?.submit();
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

                if (feed) {
                    feed.scrollTop = feed.scrollHeight;
                    setInterval(refresh, 12000);
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
                                    refresh();
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

                const api = async (url, options = {}) => {
                    const headers = Object.assign({
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf,
                    }, options.headers || {});

                    const response = await fetch(url, Object.assign({
                        credentials: 'same-origin',
                        headers,
                    }, options));

                    if (!response.ok) {
                        throw new Error('Request failed');
                    }

                    return response.json();
                };

                const closeModal = () => {
                    modal?.classList.add('hidden');
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

                    localStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: true });
                    if (localVideo) localVideo.srcObject = localStream;

                    pc = new RTCPeerConnection({ iceServers });
                    localStream.getTracks().forEach((track) => pc.addTrack(track, localStream));

                    pc.ontrack = (event) => {
                        if (remoteVideo) remoteVideo.srcObject = event.streams[0];
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
                    try {
                        const payload = await api("{{ route('calls.start', $activeTarget) }}", { method: 'POST' });
                        currentCall = payload.call;
                        showActive("{{ __('Calling...') }}");
                    } catch (_) {}
                };

                const sync = async () => {
                    if (locked) return;
                    locked = true;

                    try {
                        const payload = await api("{{ route('calls.poll') }}");
                        const call = payload.call;

                        if (!call) {
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

                        if (call.status === 'active') {
                            stopRingtone();
                            showActive("{{ __('In call') }}");

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
                            closeModal();
                            currentCall = null;
                        }
                    } catch (_) {
                    } finally {
                        locked = false;
                    }
                };

                const accept = async () => {
                    if (!currentCall) return;
                    try {
                        await api("{{ url('/calls') }}/" + currentCall.id + "/accept", { method: 'POST' });
                        stopRingtone();
                        showActive("{{ __('Connecting...') }}");
                    } catch (_) {}
                };

                const reject = async () => {
                    if (!currentCall) return;
                    try {
                        await api("{{ url('/calls') }}/" + currentCall.id + "/reject", { method: 'POST' });
                    } catch (_) {}
                    stopRingtone();
                    resetPeer();
                    closeModal();
                };

                const end = async () => {
                    if (!currentCall) return;
                    try {
                        await api("{{ url('/calls') }}/" + currentCall.id + "/end", { method: 'POST' });
                    } catch (_) {}
                    stopRingtone();
                    resetPeer();
                    closeModal();
                };

                startHeader?.addEventListener('click', startCall);
                startInline?.addEventListener('click', startCall);
                acceptBtn?.addEventListener('click', accept);
                rejectBtn?.addEventListener('click', reject);
                endBtn?.addEventListener('click', end);
                closeBtn?.addEventListener('click', closeModal);

                modal?.addEventListener('click', (event) => {
                    if (event.target === modal) closeModal();
                });

                setInterval(sync, 2000);
            })();
        </script>
    @endif
</x-app-layout>
