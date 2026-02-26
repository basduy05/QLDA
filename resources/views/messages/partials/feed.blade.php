@forelse ($messages as $message)
    <div class="flex {{ $message->user_id === $authUserId ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
        <div class="max-w-[75%] rounded-2xl px-4 py-3 {{ $message->user_id === $authUserId ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-800' }}">
            <p class="text-sm whitespace-pre-wrap">{{ $message->body }}</p>
            <p class="text-[10px] mt-2 opacity-75">{{ $message->created_at?->format('H:i') }}</p>
        </div>
    </div>
@empty
    <p class="text-sm text-slate-500">{{ __('No messages yet.') }}</p>
@endforelse
