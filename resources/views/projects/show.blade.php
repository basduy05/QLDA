<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">{{ __('Project details') }}</p>
                <h2 class="text-3xl font-semibold text-slate-900">{{ $project->name }}</h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('projects.edit', $project) }}" class="btn-secondary">{{ __('Edit') }}</a>
                <form method="POST" action="{{ route('projects.destroy', $project) }}" data-confirm="{{ __('Delete this project?') }}" onsubmit="return confirm(this.dataset.confirm)">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-secondary">{{ __('Delete') }}</button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="card p-4 text-sm text-emerald-700 bg-emerald-50">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="card-strong p-6 lg:col-span-2">
                <h3 class="text-xl font-semibold mb-4">{{ __('Overview') }}</h3>
                <p class="text-slate-600">{{ $project->description ?? __('No description provided.') }}</p>
                <div class="mt-4 grid gap-4 md:grid-cols-3 text-sm">
                    <div>
                        <p class="text-slate-500">{{ __('Owner') }}</p>
                        <p class="font-semibold text-slate-900">{{ $project->owner?->name }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">{{ __('Status') }}</p>
                        <p class="font-semibold text-slate-900">{{ __(ucwords(str_replace('_', ' ', $project->status))) }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">{{ __('Timeline') }}</p>
                        <p class="font-semibold text-slate-900">
                            {{ $project->start_date?->format('d/m/Y') ?? '—' }} → {{ $project->end_date?->format('d/m/Y') ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-strong p-6">
                <h3 class="text-xl font-semibold mb-4">{{ __('Quick actions') }}</h3>
                <a href="{{ route('projects.tasks.create', $project) }}" class="btn-primary w-full">{{ __('Add task') }}</a>
                <a href="{{ route('projects.index') }}" class="btn-secondary w-full mt-3">{{ __('Back to projects') }}</a>
            </div>
        </div>

        <div class="card-strong p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold">{{ __('Tasks') }}</h3>
                <a href="{{ route('projects.tasks.create', $project) }}" class="btn-secondary">{{ __('New task') }}</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left table-head">
                        <tr>
                            <th class="py-3">{{ __('Task') }}</th>
                            <th class="py-3">{{ __('Assignee') }}</th>
                            <th class="py-3">{{ __('Status') }}</th>
                            <th class="py-3">{{ __('Priority') }}</th>
                            <th class="py-3">{{ __('Due date') }}</th>
                            <th class="py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($tasks as $task)
                            <tr>
                                <td class="py-4">
                                    <p class="font-semibold text-slate-900">{{ $task->title }}</p>
                                    <p class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($task->description, 80) }}</p>
                                </td>
                                <td class="py-4 text-slate-600">{{ $task->assignee?->name ?? __('Unassigned') }}</td>
                                <td class="py-4">
                                    <span class="badge bg-slate-100 text-slate-700">{{ __(ucwords(str_replace('_', ' ', $task->status))) }}</span>
                                </td>
                                <td class="py-4 text-slate-600">{{ __(ucwords($task->priority)) }}</td>
                                <td class="py-4 text-slate-600">{{ $task->due_date?->format('d/m/Y') ?? '—' }}</td>
                                <td class="py-4 text-right">
                                    <a href="{{ route('tasks.edit', $task) }}" class="text-slate-600">{{ __('Edit') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-slate-500">{{ __('No tasks yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
