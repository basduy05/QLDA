<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Task') }}</p>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-200">{{ $task->title }}</h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('tasks.edit', $task) }}" class="btn-secondary">{{ __('Edit task') }}</a>
                <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this task?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">{{ __('Delete task') }}</button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Description --}}
            <div class="card-white dark:card-dark">
                <h3 class="card-header dark:card-header-dark">{{ __('Description') }}</h3>
                <div class="p-6">
                    @if($task->description)
                        <div class="prose dark:prose-invert max-w-none">
                            {!! nl2br(e($task->description)) !!}
                        </div>
                    @else
                        <p class="text-slate-500 dark:text-slate-400">{{ __('No description provided.') }}</p>
                    @endif
                </div>
            </div>

            {{-- Comments --}}
            <div class="card-white dark:card-dark">
                <h3 class="card-header dark:card-header-dark">{{ __('Comments') }}</h3>
                <div class="p-6 space-y-4">
                    @forelse ($task->comments as $comment)
                        <div class="flex items-start gap-3">
                            <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->name }}" class="w-8 h-8 rounded-full">
                            <div class="flex-1">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-semibold text-slate-900 dark:text-slate-200">{{ $comment->user->name }}</span>
                                    <span class="text-slate-500 dark:text-slate-400" title="{{ $comment->created_at->format('d/m/Y H:i:s') }}">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="mt-1 prose prose-sm dark:prose-invert max-w-none">
                                    {!! nl2br(e($comment->body)) !!}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-center text-slate-500 dark:text-slate-400 py-4">{{ __('No comments yet.') }}</p>
                    @endforelse
                </div>
                <div class="px-6 pb-6">
                    <form method="POST" action="{{ route('tasks.comments.store', $task) }}" class="space-y-3">
                        @csrf
                        <div class="flex items-start gap-3">
                            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full">
                            <div class="flex-1">
                                <textarea name="body" rows="3" class="input-dark w-full" placeholder="{{ __('Write a comment...') }}" required>{{ old('body') }}</textarea>
                                <x-input-error :messages="$errors->get('body')" class="mt-2" />
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="btn-primary">{{ __('Post comment') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="card-white dark:card-dark">
                <h3 class="card-header dark:card-header-dark">{{ __('Details') }}</h3>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ __('Project') }}</span>
                        <a href="{{ route('projects.show', $task->project) }}" class="text-sm font-semibold text-blue-600 hover:underline dark:text-blue-400">{{ $task->project->name }}</a>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ __('Assignee') }}</span>
                        @if($task->assignee)
                            <div class="flex items-center gap-2">
                                <img src="{{ $task->assignee->avatar_url }}" alt="{{ $task->assignee->name }}" class="w-6 h-6 rounded-full">
                                <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $task->assignee->name }}</span>
                            </div>
                        @else
                            <span class="text-sm text-slate-500 dark:text-slate-400 italic">{{ __('Unassigned') }}</span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ __('Status') }}</span>
                        <x-task.status-badge :status="$task->status" />
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ __('Priority') }}</span>
                        <x-task.priority-badge :priority="$task->priority" />
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ __('Due date') }}</span>
                        <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $task->due_date ? $task->due_date->format('d/m/Y') : '—' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ __('Created by') }}</span>
                        <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $task->creator->name }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ __('Created at') }}</span>
                        <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $task->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="flex justify-end">
                 <a href="{{ route('projects.show', $task->project) }}" class="btn-secondary">{{ __('Back to project') }}</a>
            </div>
        </div>
    </div>
</x-app-layout>
