<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900">{{ $task->title }}</h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('tasks.edit', $task) }}" class="px-3 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">
                    {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('tasks.destroy', $task) }}" data-confirm="{{ __('Are you sure?') }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-2 text-sm border border-slate-300 rounded-lg text-rose-600 hover:bg-rose-50">
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="bg-white border border-slate-200 rounded-lg p-6">
                <h3 class="text-lg font-bold text-slate-900 mb-4">{{ __('Description') }}</h3>
                <div class="text-slate-700 leading-relaxed">
                    @if($task->description)
                        {!! nl2br(e($task->description)) !!}
                    @else
                        <p class="text-slate-500 italic">{{ __('No description provided.') }}</p>
                    @endif
                </div>
            </div>

            <!-- Comments -->
            <div class="bg-white border border-slate-200 rounded-lg p-6">
                <h3 class="text-lg font-bold text-slate-900 mb-4">{{ __('Comments') }}</h3>
                <div class="space-y-4 mb-6 pb-6 border-b border-slate-200">
                    @forelse ($task->comments as $comment)
                        <div class="flex items-start gap-3">
                            <div class="h-8 w-8 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-xs font-bold shrink-0">
                                {{ strtoupper(substr($comment->user->name, 0, 2)) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-semibold text-slate-900">{{ $comment->user->name }}</span>
                                    <span class="text-slate-500 text-xs">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="mt-1 text-slate-700">
                                    {!! nl2br(e($comment->body)) !!}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-center text-slate-500 py-4">{{ __('No comments yet.') }}</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('tasks.comments.store', $task) }}" class="space-y-3">
                    @csrf
                    <textarea name="body" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="{{ __('Write a comment...') }}" required>{{ old('body') }}</textarea>
                    @error('body')
                        <p class="text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                            {{ __('Post comment') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white border border-slate-200 rounded-lg p-6">
                <h3 class="text-lg font-bold text-slate-900 mb-4">{{ __('Details') }}</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between items-start">
                        <span class="text-slate-500 font-medium">{{ __('Project') }}</span>
                        <a href="{{ route('projects.show', $task->project) }}" class="text-blue-600 hover:text-blue-700 font-medium">
                            {{ $task->project->name }}
                        </a>
                    </div>

                    <div class="flex justify-between items-start">
                        <span class="text-slate-500 font-medium">{{ __('Assignee') }}</span>
                        <span class="text-slate-700">
                            @if($task->assignee)
                                {{ $task->assignee->name }}
                            @else
                                <span class="italic">{{ __('Unassigned') }}</span>
                            @endif
                        </span>
                    </div>

                    <div class="flex justify-between items-start">
                        <span class="text-slate-500 font-medium">{{ __('Status') }}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($task->status === 'done') bg-emerald-100 text-emerald-700
                            @elseif($task->status === 'in_progress') bg-blue-100 text-blue-700
                            @else bg-slate-100 text-slate-700
                            @endif
                        ">
                            {{ __(ucwords(str_replace('_', ' ', $task->status))) }}
                        </span>
                    </div>

                    <div class="flex justify-between items-start">
                        <span class="text-slate-500 font-medium">{{ __('Priority') }}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($task->priority === 'high') bg-red-100 text-red-700
                            @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-700
                            @else bg-gray-100 text-gray-700
                            @endif
                        ">
                            {{ __(ucfirst($task->priority)) }}
                        </span>
                    </div>

                    <div class="flex justify-between items-start">
                        <span class="text-slate-500 font-medium">{{ __('Due date') }}</span>
                        <span class="text-slate-700">
                            {{ $task->due_date ? $task->due_date->format('d/m/Y') : '—' }}
                        </span>
                    </div>

                    <div class="flex justify-between items-start pt-4 border-t border-slate-200">
                        <span class="text-slate-500 font-medium">{{ __('Created by') }}</span>
                        <span class="text-slate-700">{{ $task->creator->name }}</span>
                    </div>

                    <div class="flex justify-between items-start">
                        <span class="text-slate-500 font-medium">{{ __('Created at') }}</span>
                        <span class="text-slate-700">{{ $task->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            <a href="{{ route('projects.show', $task->project) }}" class="block w-full text-center px-3 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 text-sm font-medium">
                {{ __('Back to project') }}
            </a>
        </div>
    </div>
</x-app-layout>
