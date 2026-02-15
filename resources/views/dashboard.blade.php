<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm text-slate-500">{{ __('Overview') }}</p>
            <h2 class="text-3xl font-semibold text-slate-900">{{ __('Project command center') }}</h2>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            <div class="card p-6">
                <p class="text-sm text-slate-500">{{ __('Total projects') }}</p>
                <p class="text-3xl font-semibold text-slate-900">{{ $projectsCount }}</p>
            </div>
            <div class="card p-6">
                <p class="text-sm text-slate-500">{{ __('Active projects') }}</p>
                <p class="text-3xl font-semibold text-slate-900">{{ $activeProjectsCount }}</p>
            </div>
            <div class="card p-6">
                <p class="text-sm text-slate-500">{{ __('Tasks in view') }}</p>
                <p class="text-3xl font-semibold text-slate-900">{{ $tasksCount }}</p>
            </div>
            <div class="card p-6">
                <p class="text-sm text-slate-500">{{ __('Open tasks') }}</p>
                <p class="text-3xl font-semibold text-slate-900">{{ $openTasksCount }}</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="card-strong p-6 lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold">{{ __('Recent projects') }}</h3>
                    <a href="{{ route('projects.index') }}" class="text-sm text-slate-500">{{ __('View all') }}</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($recentProjects as $project)
                        <div class="py-4 flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $project->name }}</p>
                                <p class="text-sm text-slate-500">{{ $project->owner?->name }} · {{ __($project->status) }}</p>
                            </div>
                            <a href="{{ route('projects.show', $project) }}" class="text-sm text-slate-600">{{ __('Open') }}</a>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 py-6">{{ __('No projects yet.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="card-strong p-6">
                <h3 class="text-xl font-semibold mb-4">{{ __('Upcoming tasks') }}</h3>
                <div class="space-y-4">
                    @forelse ($upcomingTasks as $task)
                        <div>
                            <p class="font-semibold text-slate-900">{{ $task->title }}</p>
                            <p class="text-sm text-slate-500">{{ $task->project?->name }} · {{ $task->due_date?->format('d/m/Y') }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">{{ __('No upcoming tasks.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card-strong p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold">{{ __('Overdue tasks') }}</h3>
                <a href="{{ route('tasks.index') }}" class="text-sm text-slate-500">{{ __('Manage tasks') }}</a>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                @forelse ($overdueTasks as $task)
                    <div class="card p-4">
                        <p class="font-semibold text-slate-900">{{ $task->title }}</p>
                        <p class="text-sm text-slate-500">{{ $task->project?->name }} · {{ $task->due_date?->format('d/m/Y') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">{{ __('No overdue tasks.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
