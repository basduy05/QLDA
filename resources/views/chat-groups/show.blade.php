<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm text-slate-500">{{ __('Group Chat') }}</p>
                <h2 class="text-3xl font-semibold text-slate-900">{{ $group->name }}</h2>
                <p class="text-sm text-slate-500 mt-2">{{ __('Messages expire after 24 hours.') }}</p>
            </div>
            <a href="{{ route('chat-groups.index') }}" class="btn-secondary">{{ __('Back') }}</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="card p-4 text-sm text-emerald-700 bg-emerald-50">
                {{ session('status') }}
            </div>
        @endif

        <div class="card-strong p-6">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">{{ __('Members') }}</h3>
            <div class="flex flex-wrap gap-2">
                @foreach ($group->members as $member)
                    <span class="badge bg-slate-100 text-slate-700">{{ $member->name }}</span>
                @endforeach
            </div>
        </div>

        <div class="card-strong p-6">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">{{ __('Messages') }}</h3>
            <div id="messages-list" class="max-h-[500px] overflow-y-auto space-y-3 mb-4">
                @include('chat-groups.partials.messages', ['messages' => $messages])
            </div>

            <form method="POST" action="{{ route('chat-groups.messages.store', $group) }}" class="space-y-3">
                @csrf
                <div>
                    <textarea name="body" rows="3" class="w-full rounded-xl border-slate-200" placeholder="{{ __('Type a message...') }}" required>{{ old('body') }}</textarea>
                    @error('body')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <button type="submit" class="btn-primary">{{ __('Send') }}</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const list = document.getElementById('messages-list');
            const textarea = document.querySelector('textarea[name="body"]');
            if (!list) {
                return;
            }

            let isLoading = false;
            const endpoint = "{{ route('chat-groups.messages.index', $group) }}";

            const refreshMessages = async () => {
                if (isLoading) {
                    return;
                }

                if (textarea && document.activeElement === textarea) {
                    return;
                }

                isLoading = true;
                const nearBottom = list.scrollHeight - list.scrollTop - list.clientHeight < 120;

                try {
                    const response = await fetch(endpoint, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    });

                    if (!response.ok) {
                        return;
                    }

                    const html = await response.text();
                    list.innerHTML = html;

                    if (nearBottom) {
                        list.scrollTop = list.scrollHeight;
                    }
                } finally {
                    isLoading = false;
                }
            };

            setInterval(refreshMessages, 5000);
            list.scrollTop = list.scrollHeight;
        })();
    </script>
</x-app-layout>
