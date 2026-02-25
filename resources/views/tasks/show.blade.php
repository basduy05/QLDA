<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('projects.show', $task->project) }}" class="text-sm font-medium text-slate-500 hover:text-accent transition-colors uppercase tracking-wider">{{ $task->project->name }}</a>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    <span class="text-sm font-medium text-slate-400 uppercase tracking-wider">{{ __('Task Details') }}</span>
                </div>
                <h2 class="text-3xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
                    {{ $task->title }}
                </h2>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('tasks.edit', $task) }}" class="btn-secondary inline-flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                    {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('tasks.destroy', $task) }}" data-confirm="{{ __('Are you sure you want to delete this task?') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-secondary text-rose-600 hover:bg-rose-50 hover:border-rose-200 hover:text-rose-700 inline-flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Description --}}
            <div class="card-strong p-6">
                <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    {{ __('Description') }}
                </h3>
                <div class="prose prose-sm prose-slate max-w-none">
                    @if($task->description)
                        <p class="text-slate-600 leading-relaxed">{!! nl2br(e($task->description)) !!}</p>
                    @else
                        <p class="text-slate-400 italic">{{ __('No description provided.') }}</p>
                    @endif
                </div>
            </div>

            {{-- Comments --}}
            <div class="card-strong p-6">
                <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    {{ __('Comments') }}
                </h3>
                <div class="space-y-4 mb-6">
                    @forelse ($task->comments as $comment)
                        <div class="flex items-start gap-3">
                            <div class="h-8 w-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 font-bold shrink-0 text-xs">
                                {{ strtoupper(substr($comment->user->name, 0, 2)) }}
                            </div>
                            <div class="flex-1 bg-slate-50 rounded-2xl rounded-tl-none p-3 border border-slate-100">
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-semibold text-slate-900">{{ $comment->user->name }}</span>
                                    <span class="text-slate-500" title="{{ $comment->created_at->format('d/m/Y H:i:s') }}">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-sm text-slate-700">
                                    {!! nl2br(e($comment->body)) !!}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-center text-slate-500 py-4">{{ __('No comments yet.') }}</p>
                    @endforelse
                </div>
                <div class="pt-4 border-t border-slate-100">
                    <form method="POST" action="{{ route('tasks.comments.store', $task) }}" class="space-y-3">
                        @csrf
                        <div class="flex items-start gap-3">
                            <div class="h-8 w-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 font-bold shrink-0 text-xs">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                            <div class="flex-1">
                                <textarea name="body" rows="2" class="w-full rounded-xl border-slate-200 text-sm focus:ring-accent focus:border-accent" placeholder="{{ __('Write a comment...') }}" required>{{ old('body') }}</textarea>
                                <x-input-error :messages="$errors->get('body')" class="mt-2" />
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="btn-primary text-xs py-1.5 px-4">{{ __('Post comment') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="card-strong p-6 bg-gradient-to-br from-white to-slate-50/50">
                <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    {{ __('Details') }}
                </h3>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                        <span class="font-medium text-slate-500">{{ __('Project') }}</span>
                        <a href="{{ route('projects.show', $task->project) }}" class="font-semibold text-slate-900 hover:text-accent transition-colors">{{ $task->project->name }}</a>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                        <span class="font-medium text-slate-500">{{ __('Assignee') }}</span>
                        @if($task->assignee)
                            <div class="flex items-center gap-2">
                                <div class="h-6 w-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-medium text-slate-600 shrink-0">
                                    {{ strtoupper(substr($task->assignee->name, 0, 2)) }}
                                </div>
                                <span class="font-semibold text-slate-900">{{ $task->assignee->name }}</span>
                            </div>
                        @else
                            <span class="text-slate-400 italic">{{ __('Unassigned') }}</span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                        <span class="font-medium text-slate-500">{{ __('Status') }}</span>
                        @php
                            $statusColors = [
                                'todo' => 'bg-slate-100 text-slate-700 border-slate-200',
                                'in_progress' => 'bg-sky-100 text-sky-700 border-sky-200',
                                'done' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            ];
                            $colorClass = $statusColors[$task->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $colorClass }}">
                            {{ __(ucwords(str_replace('_', ' ', $task->status))) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                        <span class="font-medium text-slate-500">{{ __('Priority') }}</span>
                        @php
                            $priorityColors = [
                                'low' => 'text-slate-500 bg-slate-50 border-slate-200',
                                'medium' => 'text-amber-600 bg-amber-50 border-amber-200',
                                'high' => 'text-rose-600 bg-rose-50 border-rose-200',
                            ];
                            $pColorClass = $priorityColors[$task->priority] ?? 'text-slate-500 bg-slate-50 border-slate-200';
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border {{ $pColorClass }}">
                            {{ __(ucwords($task->priority)) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                        <span class="font-medium text-slate-500">{{ __('Due date') }}</span>
                        <span class="font-semibold text-slate-900">{{ $task->due_date ? $task->due_date->format('d/m/Y') : '—' }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                        <span class="font-medium text-slate-500">{{ __('Created by') }}</span>
                        <span class="font-semibold text-slate-900">{{ $task->creator->name }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-slate-500">{{ __('Created at') }}</span>
                        <span class="font-semibold text-slate-900">{{ $task->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
