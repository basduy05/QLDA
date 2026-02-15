<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm text-slate-500">{{ __('Task details') }}</p>
            <h2 class="text-3xl font-semibold text-slate-900">{{ $task->title }}</h2>
        </div>
    </x-slot>

    <div class="card-strong p-6 space-y-6">
        <div>
            <p class="text-sm text-slate-500">{{ __('Project') }}</p>
            <p class="text-lg font-semibold text-slate-900">{{ $task->project?->name }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">{{ __('Description') }}</p>
            <p class="text-slate-600">{{ $task->description ?? __('No description provided.') }}</p>
        </div>
        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <p class="text-sm text-slate-500">{{ __('Status') }}</p>
                <p class="font-semibold text-slate-900">{{ __(ucwords(str_replace('_', ' ', $task->status))) }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">{{ __('Priority') }}</p>
                <p class="font-semibold text-slate-900">{{ __(ucwords($task->priority)) }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">{{ __('Due date') }}</p>
                <p class="font-semibold text-slate-900">{{ $task->due_date?->format('d/m/Y') ?? '—' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('tasks.edit', $task) }}" class="btn-secondary">{{ __('Edit task') }}</a>
            <a href="{{ route('projects.show', $task->project) }}" class="btn-secondary">{{ __('Back to project') }}</a>
        </div>
    </div>

    <div class="card-strong p-6 space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold">{{ __('Comments') }}</h3>
            <span class="text-sm text-slate-500">{{ __('Tag with @name') }}</span>
        </div>

        @if (session('status'))
            <div class="card p-4 text-sm text-emerald-700 bg-emerald-50">
                {{ session('status') }}
            </div>
        @endif

        <div class="space-y-4">
            @forelse ($task->comments as $comment)
                <div class="card p-4">
                    <div class="flex items-center justify-between text-sm text-slate-500">
                        <span class="font-semibold text-slate-900">{{ $comment->user?->name }}</span>
                        <span>{{ $comment->created_at?->format('d/m/Y H:i') }}</span>
                    </div>
                    <p class="text-slate-700 mt-2">{{ $comment->body }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-500">{{ __('No comments yet.') }}</p>
            @endforelse
        </div>

        <form method="POST" action="{{ route('tasks.comments.store', $task) }}" class="space-y-3">
            @csrf
            <textarea name="body" rows="3" class="w-full rounded-xl border-slate-200" placeholder="{{ __('Write a comment...') }}" required>{{ old('body') }}</textarea>
            <x-input-error :messages="$errors->get('body')" class="mt-2" />
            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary">{{ __('Post comment') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
