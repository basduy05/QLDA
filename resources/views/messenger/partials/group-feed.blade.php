@forelse ($messages as $message)
    @if ($message->is_system)
        <div class="flex justify-center">
            <p class="max-w-[90%] rounded-full bg-slate-100 px-3 py-1 text-[11px] text-slate-600 text-center">{{ $message->body }}</p>
        </div>
        @continue
    @endif

    @php($mine = $message->user_id === $authUserId)
    @php($sender = $nicknames[$message->user_id] ?? $message->user?->name)
    <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
        <div class="max-w-[78%] rounded-2xl px-4 py-2 {{ $mine ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-800' }}">
            @unless($mine)
                <p class="text-[11px] font-semibold mb-1 opacity-80">{{ $sender }}</p>
            @endunless
            <p class="text-sm whitespace-pre-wrap">{{ $message->body }}</p>
            <p class="mt-1 text-[10px] opacity-70 text-right">{{ $message->created_at?->format('H:i') }}</p>
        </div>
    </div>
@empty
    <p class="text-sm text-slate-500">{{ __('No messages yet.') }}</p>
@endforelse
