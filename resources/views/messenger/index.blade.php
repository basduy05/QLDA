<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm text-slate-500">{{ __('Messenger') }}</p>
                <h2 class="text-2xl font-semibold text-slate-900">{{ __('Chats & Groups') }}</h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('chat-groups.index') }}" class="btn-secondary text-xs">{{ __('Manage Groups') }}</a>
                @if ($callPrimary)
                    <button type="button" id="open-call-primary" class="btn-primary text-xs">{{ __('Call (Embedded)') }}</button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="card-strong p-0 overflow-hidden">
        <div class="grid md:grid-cols-12 min-h-[calc(100vh-220px)]">
            <aside class="md:col-span-4 lg:col-span-3 border-r border-slate-100 p-3 overflow-y-auto">
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

            <section class="md:col-span-8 lg:col-span-9 p-3 flex flex-col">
                @if ($activeType)
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <div>
                            <p class="font-semibold text-slate-900">
                                {{ $activeType === 'direct' ? $activeTarget->name : $activeTarget->name }}
                            </p>
                            <p class="text-xs text-slate-500" id="typing-indicator">
                                {{ $activeType === 'direct' && $typing ? __('Typing...') : '' }}
                            </p>
                        </div>
                        @if ($callPrimary)
                            <div class="flex items-center gap-2">
                                <button type="button" id="open-call-inline" class="btn-secondary text-xs">{{ __('Start Call') }}</button>
                                @if ($callBackup)
                                    <button type="button" id="open-call-backup" class="btn-secondary text-xs">{{ __('Backup Call') }}</button>
                                @endif
                            </div>
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

    @if ($activeType)
        <script>
            (function () {
                const feed = document.getElementById('messenger-feed');
                const composer = document.getElementById('composer');
                const form = document.getElementById('composer-form');
                const typingEl = document.getElementById('typing-indicator');
                const type = "{{ $activeType }}";
                const feedEndpoint = type === 'direct'
                    ? "{{ $activeType === 'direct' ? route('messenger.direct-feed', $activeTarget) : '' }}"
                    : "{{ $activeType === 'group' ? route('messenger.group-feed', $activeTarget) : '' }}";
                const typingEndpoint = "{{ $activeType === 'direct' ? route('messenger.typing', $activeTarget) : '' }}";
                let loading = false;
                let typingTick = 0;

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
                            if (form) {
                                form.submit();
                            }
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
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            },
                            credentials: 'same-origin',
                        });
                    });
                }

                if (feed) {
                    feed.scrollTop = feed.scrollHeight;
                    setInterval(refresh, 3500);
                }
            })();
        </script>
    @endif

    @if ($callPrimary)
        <div id="call-config" data-primary="{{ $callPrimary }}" data-backup="{{ $callBackup ?? '' }}" class="hidden"></div>

        <div id="call-modal" class="fixed inset-0 z-50 hidden bg-slate-900/70 p-4">
            <div class="mx-auto h-full max-w-6xl rounded-2xl bg-white shadow-xl flex flex-col overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                    <h3 class="font-semibold text-slate-900">{{ __('Embedded Call') }}</h3>
                    <div class="flex items-center gap-2">
                        @if ($callBackup)
                            <button type="button" id="switch-call-backup" class="btn-secondary text-xs">{{ __('Switch Backup') }}</button>
                        @endif
                        <button type="button" id="close-call" class="btn-secondary text-xs">{{ __('Close') }}</button>
                    </div>
                </div>
                <iframe id="call-frame" class="w-full flex-1" allow="camera; microphone; fullscreen; display-capture" referrerpolicy="strict-origin-when-cross-origin"></iframe>
            </div>
        </div>

        <script>
            (function () {
                const modal = document.getElementById('call-modal');
                const frame = document.getElementById('call-frame');
                const openPrimaryHeader = document.getElementById('open-call-primary');
                const openPrimaryInline = document.getElementById('open-call-inline');
                const openBackupInline = document.getElementById('open-call-backup');
                const switchBackup = document.getElementById('switch-call-backup');
                const closeCall = document.getElementById('close-call');
                const callConfig = document.getElementById('call-config');

                const primaryUrl = callConfig?.dataset.primary || '';
                const backupUrl = callConfig?.dataset.backup || '';

                const openWith = (url) => {
                    if (!modal || !frame || !url) {
                        return;
                    }
                    frame.src = url;
                    modal.classList.remove('hidden');
                };

                const close = () => {
                    if (!modal || !frame) {
                        return;
                    }
                    frame.src = '';
                    modal.classList.add('hidden');
                };

                openPrimaryHeader?.addEventListener('click', () => openWith(primaryUrl));
                openPrimaryInline?.addEventListener('click', () => openWith(primaryUrl));
                openBackupInline?.addEventListener('click', () => openWith(backupUrl));
                switchBackup?.addEventListener('click', () => openWith(backupUrl));
                closeCall?.addEventListener('click', close);

                modal?.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        close();
                    }
                });
            })();
        </script>
    @endif
</x-app-layout>
