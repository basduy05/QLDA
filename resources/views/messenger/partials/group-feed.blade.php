@forelse ($messages as $message)
    @php($mine = $message->user_id === $authUserId)
    <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
        <div class="max-w-[78%] rounded-2xl px-4 py-2 {{ $mine ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-800' }}">
            @unless($mine)
                <p class="text-[11px] font-semibold mb-1 opacity-80">{{ $message->user?->name }}</p>
            @endunless
            <p class="text-sm whitespace-pre-wrap">{{ $message->body }}</p>
            <p class="mt-1 text-[10px] opacity-70 text-right">{{ $message->created_at?->format('H:i') }}</p>
        </div>
    </div>
@empty
    <p class="text-sm text-slate-500">{{ __('No messages yet.') }}</p>
@endforelse
