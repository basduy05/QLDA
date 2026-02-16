@forelse ($messages as $message)
    @php($mine = $message->user_id === $authUserId)
    <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
        <div class="max-w-[78%] rounded-2xl px-4 py-2 {{ $mine ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-800' }}">
            <p class="text-sm whitespace-pre-wrap">{{ $message->body }}</p>
            <div class="mt-1 flex items-center justify-end gap-2 text-[10px] opacity-75">
                <span>{{ $message->created_at?->format('H:i') }}</span>
                @if ($mine)
                    <span>{{ $message->seen_at ? __('Seen') : __('Sent') }}</span>
                @endif
            </div>
        </div>
    </div>
@empty
    <p class="text-sm text-slate-500">{{ __('No messages yet.') }}</p>
@endforelse
