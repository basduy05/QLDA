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
</x-app-layout>
