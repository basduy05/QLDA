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
            <div class="max-h-[500px] overflow-y-auto space-y-3 mb-4">
                @forelse ($messages as $message)
                    <div class="card p-4">
                        <div class="flex items-center justify-between gap-3">
                            <p class="font-semibold text-slate-900">{{ $message->user?->name }}</p>
                            <p class="text-xs text-slate-500">{{ $message->created_at?->format('d/m/Y H:i') }}</p>
                        </div>
                        <p class="text-sm text-slate-700 mt-2 whitespace-pre-wrap">{{ $message->body }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">{{ __('No messages yet.') }}</p>
                @endforelse
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
</x-app-layout>
