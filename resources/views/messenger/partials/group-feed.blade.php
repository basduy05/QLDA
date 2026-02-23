@forelse ($messages as $message)
    @if ($message->is_system)
        <div class="message-system flex justify-center">
            <p class="max-w-[90%] rounded-full bg-slate-100 px-3 py-1 text-[11px] text-slate-600 text-center">{{ $message->body }}</p>
        </div>
        @continue
    @endif

    @php
        $mine = (int) $message->user_id === (int) $authUserId;
        $sender = $nicknames[$message->user_id] ?? $message->user?->name;
        $hasAttachment = filled($message->attachment_path);
        $attachmentUrl = $hasAttachment ? asset('storage/' . ltrim((string) $message->attachment_path, '/')) : null;
    @endphp
    <div class="message-row flex {{ $mine ? 'justify-end' : 'justify-start' }}">
        <div class="message-bubble max-w-[92%] md:max-w-[78%] rounded-2xl px-4 py-2 {{ $mine ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-800' }}">
            @unless($mine)
                <p class="text-[11px] font-semibold mb-1 opacity-80">{{ $sender }}</p>
            @endunless

            @if (trim((string) $message->body) !== '')
                <p class="text-sm whitespace-pre-wrap">{{ $message->body }}</p>
            @endif

            @if ($hasAttachment)
                @php
                    $attachmentSize = $message->attachment_size ? number_format(((int) $message->attachment_size) / 1024, 1) . ' KB' : null;
                @endphp
                <a href="{{ $attachmentUrl }}" target="_blank" rel="noopener noreferrer" class="mt-2 flex items-center justify-between gap-2 rounded-xl border {{ $mine ? 'border-white/25 bg-white/10 text-white' : 'border-slate-200 bg-white text-slate-700' }} px-3 py-2 text-xs">
                    <span class="truncate">📎 {{ $message->attachment_name ?? __('Attachment') }}</span>
                    @if ($attachmentSize)
                        <span class="shrink-0 opacity-80">{{ $attachmentSize }}</span>
                    @endif
                </a>
            @endif

            <p class="mt-1 text-[10px] opacity-70 text-right">{{ $message->created_at?->format('H:i') }}</p>
        </div>
    </div>
@empty
    <p class="text-sm text-slate-500">{{ __('No messages yet.') }}</p>
@endforelse
