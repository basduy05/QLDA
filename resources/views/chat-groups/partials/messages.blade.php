@forelse ($messages as $message)
    <div class="card p-4" data-message-id="{{ $message->id }}">
        <div class="flex items-center justify-between gap-3">
            <p class="font-semibold text-slate-900">{{ $message->user?->name }}</p>
            <p class="text-xs text-slate-500">{{ $message->created_at?->format('d/m/Y H:i') }}</p>
        </div>
        <p class="text-sm text-slate-700 mt-2 whitespace-pre-wrap">{{ $message->body }}</p>
    </div>
@empty
    <p class="text-sm text-slate-500">{{ __('No messages yet.') }}</p>
@endforelse
