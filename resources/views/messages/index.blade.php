<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm text-slate-500">{{ __('Messenger') }}</p>
                <h2 class="text-3xl font-semibold text-slate-900">{{ __('Private Messages') }}</h2>
            </div>
            @if ($callUrl)
                <a href="{{ $callUrl }}" target="_blank" rel="noopener noreferrer" class="btn-primary">{{ __('Call') }}</a>
            @endif
        </div>
    </x-slot>

    <div class="card-strong p-0 overflow-hidden">
        <div class="grid md:grid-cols-3 min-h-[70vh]">
            <aside class="border-r border-slate-100 p-4 overflow-y-auto">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-widest mb-4">{{ __('Contacts') }}</h3>
                <div class="space-y-2">
                    @foreach ($contacts as $contact)
                        @php($meta = $conversationMap->get($contact->id))
                        <a href="{{ route('messages.show', $contact) }}"
                           class="block rounded-xl border px-3 py-3 transition {{ optional($activeContact)->id === $contact->id ? 'border-slate-900 bg-slate-50' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                            <p class="font-semibold text-slate-900">{{ $contact->name }}</p>
                            <p class="text-xs text-slate-500">{{ $contact->email }}</p>
                            @if ($meta && $meta['last_message'])
                                <p class="text-xs text-slate-500 mt-1">{{ \Illuminate\Support\Str::limit($meta['last_message'], 40) }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </aside>

            <section class="md:col-span-2 p-4 flex flex-col">


                @if ($activeContact)
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $activeContact->name }}</p>
                            <p class="text-xs text-slate-500">{{ $activeContact->email }}</p>
                        </div>
                        @if ($callUrl)
                            <a href="{{ $callUrl }}" target="_blank" rel="noopener noreferrer" class="btn-secondary">{{ __('Voice Call') }}</a>
                        @endif
                    </div>

                    <div id="dm-feed" class="flex-1 overflow-y-auto py-4 space-y-3">
                        @include('messages.partials.feed', ['messages' => $messages, 'authUserId' => auth()->id()])
                    </div>

                    <form method="POST" action="{{ route('messages.store', $activeContact) }}" class="pt-3 border-t border-slate-100">
                        @csrf
                        <div class="flex gap-2">
                            <textarea name="body" rows="2" class="w-full rounded-xl border-slate-200" placeholder="{{ __('Type a message...') }}" required>{{ old('body') }}</textarea>
                            <button type="submit" class="btn-primary">{{ __('Send') }}</button>
                        </div>
                        @error('body')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </form>
                @else
                    <div class="h-full flex items-center justify-center text-slate-500 text-sm">
                        {{ __('Select a contact to start chatting.') }}
                    </div>
                @endif
            </section>
        </div>
    </div>

    @if ($activeContact)
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const feed = document.getElementById('dm-feed');
                if (!feed) return;

                // Initial scroll
                feed.scrollTop = feed.scrollHeight;

                const authId = {{ Auth::id() }};
                const contactId = {{ $activeContact->id }};

                if (window.realtime) {
                    window.realtime.subscribe(`user.${authId}`);

                    window.realtime.on('direct-message.new', (payload) => {
                        console.log('New message received:', payload);
                        
                        // Prevent duplicates if already rendered
                        if (document.querySelector(`[data-message-id="${payload.id}"]`)) {
                            return;
                        }

                        // Check if message belongs to this conversation
                        // Either from me to contact, or from contact to me
                        const validContext = 
                            (payload.user_id === authId && payload.direct_conversation_id === {{ $conversation->id }}) ||
                            (payload.user_id === contactId && payload.direct_conversation_id === {{ $conversation->id }});

                        if (!validContext) return;

                        // Identify if message is mine
                        const isMe = payload.user_id === authId;
                        
                        // Create element
                        const wrapper = document.createElement('div');
                        wrapper.className = isMe ? 'flex justify-end' : 'flex justify-start';
                        
                        const bubble = document.createElement('div');
                        bubble.className = `max-w-[75%] rounded-2xl px-4 py-3 ${isMe ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-800'}`;
                        
                        const text = document.createElement('p');
                        text.className = 'text-sm whitespace-pre-wrap';
                        text.textContent = payload.body;
                        
                        const time = document.createElement('p');
                        time.className = 'text-[10px] mt-2 opacity-75';
                        const date = new Date(payload.created_at);
                        time.textContent = date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');
                        
                        bubble.appendChild(text);
                        bubble.appendChild(time);
                        wrapper.appendChild(bubble);
                        
                        feed.appendChild(wrapper);
                        
                        // Auto scroll if near bottom
                        const nearBottom = feed.scrollHeight - feed.scrollTop - feed.clientHeight < 120;
                        if (nearBottom) {
                            feed.scrollTop = feed.scrollHeight;
                        }
                    });
                }
            });
        </script>
    @endif
</x-app-layout>
